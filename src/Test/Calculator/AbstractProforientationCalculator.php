<?php
/**
 * @author: adavydov
 * @since: 09.12.2020
 */

declare(strict_types=1);

namespace App\Test\Calculator;

use App\Test\AbstractCalculator;
use App\Test\AnswersHolder;
use App\Test\Helper\ProfessionsMapper;
use App\Test\Proforientation\Calc\CalculationTypesValues;
use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculator;
use App\Test\Proforientation\Calc\TopTypesCalculator;
use App\Test\Proforientation\Calc\UserTypesCalculator;
use App\Test\Proforientation\Mapper\ConfigMapper;
use App\Test\Proforientation\Profession;
use App\Test\Proforientation\ProftestConfig;
use App\Test\QuestionsHolder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Результаты:
 * Недовольный человек (выпали всякие арты, хотя он математик)
 * http://pg/test/result/?code=guy_012023
 * 1) Дизайнер интерьера
 * 2) Uх-дизайнер
 * 3) ГРАФИЧЕСКИЙ ДИЗАЙНЕР
 * 4) Гейм-дизайнер
 * 5) МЕНЕДЖЕР КАЧЕСТВА
 * 6) Бизнес-аналитик
 * 7) Логист
 * 8) Байер
 * 9) Специалист по продажам
 * 10) Специалист по информационной безопасности
 * 11) Аналитик больших данных
 * 12) Тестировщик программного обеспечения
 * 13) Программист
 * 14) Менеджер IT-проекта
 * 15) Стилист-имиджмейкер
 *
 * Я (выпали арты, хотя it выше - это из-за присутсвия такого понятия как топ" - попали в топ - дальше идет подбор)
 * http://pg/test/result/?code=artem_012023
 * 1) ГРАФИЧЕСКИЙ ДИЗАЙНЕР
 * 2) Специалист по информационной безопасности
 * 3) Программист
 * 4) Тестировщик программного обеспечения
 * 5) Режиссёр
 * 6) Кинооператор
 * 7) Звукорежиссёр
 * 8) Дизайнер
 * 9) Композитор
 * 10) Музыкант-исполнитель
 * 11) Художник-иллюстратор
 *
 * Мама (мама довольна этим результатом, хотя зоопсихолог зачем-то попался)
 * http://pg/test/result/?code=mama_test_012023
 * 1) продюсер
 * 2) арт-дилер
 * 3) маркетологи
 * 4) режиссёр
 * 5) искусствовед
 * 6) Предприниматель
 * 7) Стилист-имиджмейкер
 * 8) психолог
 * 9) PR-менеджер
 * 10) Гид-экскурсовод
 * 11) Менеджер по персоналу
 * 12) ЛОГОПЕД, ДЕФЕКТОЛОГ
 * 13) УЧИТЕЛЬ
 * 14) Дипломат
 * 15) Журналист
 *
 * Лёша (Лёша согласен, что предприниматель первый)
 * http://pg/test/result/?code=alesha_012023
 * 1) Предприниматель
 * 2) Продакт-менеджер
 * 3) Спортивный менеджер
 * 4) Uх-дизайнер
 * 5) Арт-дилер
 * 6) МАРКЕТОЛОГ
 * 7) Продюсер
 * 8) Финансовый брокер
 * 9) Менеджер IT-проекта
 * 10) МЕНЕДЖЕР КАЧЕСТВА
 * 11) Логист
 * 12) Специалист по продажам
 * 13) Байер
 * 14) НАЛОГОВЫЙ КОНСУЛЬТАНТ
 * 15) Бизнес-аналитик
 *
 * Катя (Катя со всем согласна, хоть и неприятно осознавать, что ты hoz: 90, craft: 80, а всё остальнео низкое):
 * http://pg/test/result/?code=kate_012023
 */
abstract class AbstractProforientationCalculator extends AbstractCalculator
{
    const MAXIMUM_PROFESSIONS_NUMBER = 15;

    private ProftestConfig $config;

    public function __construct(
        AnswersHolder $answersHolder,
        QuestionsHolder $questionsHolder,
        KernelInterface $kernel,
        string $locale = 'ru')
    {
        parent::__construct($answersHolder, $questionsHolder, $kernel, $locale);
        $this->initConfig();
    }

    public function calculate(): array
    {
        $this->fitVersion();

        // считаем сколько набрали в каждом типе: усилия, интересы, скилы
        $userTypes = (new UserTypesCalculator($this->questionsHolder, $this->answersHolder))->calculate();
        // считаем среднее для каждого типа
        $avgUserTypes = self::avgValueByTypes($userTypes); // [art => 65]
        // Топовые типы
        $bestUserTypes = (new TopTypesCalculator)->calc($avgUserTypes); // [art => 65]

        $professions = $this->getProfessions();
        // расчитаем и выставим очки профессиям
        self::scoreProfessions($professions, $bestUserTypes);
        // отфильтруем профессии с низкими очками
        self::filterLowScoredProfessions($professions);
        // отсортируем профессии по очкам
        self::sortProfessions($professions);
        // отрежем по максимуму
        self::sliceProfessions($professions, self::MAXIMUM_PROFESSIONS_NUMBER);

        return [
            'professions' => $professions,
            'types_descriptions' => $this->typesDescriptions($userTypes),
            'types_top' => $bestUserTypes, // being used in trial report
        ];
    }

    /**
     * Среднее значение по типу
     * @param CalculationTypesValues $typesValuesCalculation
     * @return array ['tech' => 34.3, 'body' => 16.6, 'human' => 40]
     */
    private static function avgValueByTypes(CalculationTypesValues $typesValuesCalculation): array
    {
        $result = [];
        foreach ($typesValuesCalculation->all() as $name => $values) {
            $sum = $values->force() + $values->skills() + $values->interest();
            $result[$name] = round($sum / 3);
        }
        arsort($result);
        return $result;
    }

    /**
     * Рассчитывает и выставляет очки профессии с использованием набранных значений
     * @param Profession[] $professions
     * @param array $userTypes ['tech' => 20, 'body' => 50, 'human' => 0]
     * @return void with added scores
     */
    private static function scoreProfessions(array $professions, array $userTypes): void
    {
        // todo подумать, как дополнительно фильтровать профессии для Art: музыка/дизайн (есть вопросы про музыку/диайны)

        $calculator = new ProfessionTypeScoreCalculator($userTypes);
        foreach ($professions as $i => $profession) {
            $score = $calculator->calculate($profession->types(), $profession->typesNot());
            // todo предлагаю обойтись без топа типов.
            //  вместо этого сделать все типы частью формулы
            //  но нужно учесть, что больше типов еще не означает более точное совпадение
            //  значит их число в комбинации нельзя учитывать. Интересная задача
            $profession->setRating((int)$score);
        }
    }

    /**
     * @param Profession[] $professions
     */
    private static function filterLowScoredProfessions(array &$professions)
    {
        foreach ($professions as $i => $profession) {
            if ($profession->getRating() == 0) {
                unset($professions[$i]);
            }
        }
    }

    /**
     * Сортирует профессии по очкам
     * @param array $professions
     */
    private static function sortProfessions(array &$professions)
    {
        usort($professions, function (Profession $a, Profession $b) {
            return $b->getRating() <=> $a->getRating();
        });
    }

    /**
     * Отрезает по максимуму
     * @param array $professions
     * @param int $sliceNumber
     */
    private static function sliceProfessions(array &$professions, int $sliceNumber)
    {
        $professions = array_slice($professions, 0, $sliceNumber);
    }

    /**
     * Подбирает текстовые описания всех типов по значениям групп: интересы (среднее от усилий интересов) и способности
     * @param CalculationTypesValues $userTypes
     * @return array вида ['tech' => ['interest' => '...', 'skills' => '...'], 'natural' => ...]
     */
    private function typesDescriptions(CalculationTypesValues $userTypes): array
    {
        $descriptions = [];

        foreach ($userTypes->all() as $name => $values) {
            $interestValue = ($values->force() + $values->interest()) / 2; // интерес - это среднее от force + interest
            $skillValue = $values->skills();

            $configType = $this->config->types()->get($name);

            if ($interestValue >= 66) {
                $interestAbsoluteValue = 2;
                $interestText = $configType->interest()->maxText();
            } elseif ($interestValue >= 33) {
                $interestAbsoluteValue = 1;
                $interestText = $configType->interest()->midText();
            } else {
                $interestAbsoluteValue = 0;
                $interestText = $configType->interest()->minText();
            }

            if ($skillValue >= 66) {
                $skillsAbsoluteValue = 2;
                $skillsText = $configType->skill()->maxText();
            } elseif ($skillValue >= 33) {
                $skillsAbsoluteValue = 1;
                $skillsText = $configType->skill()->midText();
            } else {
                $skillsAbsoluteValue = 0;
                $skillsText = $configType->skill()->minText();
            }

            $descriptions[$name] = [
                'name' => $configType->name(),
                'interest' => [
                    'text' => $interestText,
                    'absolute' => $interestAbsoluteValue
                ],
                'skills' => [
                    'text' => $skillsText,
                    'absolute' => $skillsAbsoluteValue
                ],
            ];
        }

        return $descriptions;
    }

    protected function fitVersion(): void
    {
        // 2.0.1 - 26.12.2020
        // added question 723
        // Rule: Calculator must ignore question if no corresponding answer
        if (!$this->answersHolder->has('723') && $this->questionsHolder->has('723')) {
            $this->questionsHolder->remove('723');
        }
    }

    /**
     * @return Profession[]
     */
    private function getProfessions(): array
    {
        $xml = $this->kernel->getProjectDir() . '/xml/proftest/professions.xml';
        return (new ProfessionsMapper(file_get_contents($xml), $this->locale))->getProfessions();
    }

    private function initConfig()
    {
        $xml = $this->kernel->getProjectDir() . '/xml/proftest/config.xml';
        $this->config = (new ConfigMapper(file_get_contents($xml), $this->locale))->parse();
    }
}