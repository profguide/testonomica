<?php
/**
 * @author: adavydov
 * @since: 09.12.2020
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Test\AnswersHolder;
use App\Test\Helper\ProfessionsMapper;
use App\Test\Proforientation\Calc\CalculationTypesValues;
use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculator;
use App\Test\Proforientation\Calc\Values;
use App\Test\Proforientation\Mapper\ConfigMapper;
use App\Test\Proforientation\Profession;
use App\Test\Proforientation\ProftestConfig;
use App\Test\Proforientation\TypesCombination;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;
use Symfony\Component\HttpKernel\KernelInterface;

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

    protected function fitVersion(): void
    {
        // 2.0.1 - 26.12.2020
        // added question 723
        // Rule: Calculator must ignore question if no corresponding answer
        if (!$this->answersHolder->has(723) && $this->questionsHolder->has(723)) {
            $this->questionsHolder->remove(723);
        }
    }

    public function calculate(): array
    {
        $this->fitVersion();

        // считаем сколько набрали в каждом типе: усилия, интересы, скилы
        $typesCalculation = $this->calculateUserTypes();
        // считаем среднее для каждого типа
        $avgValueByTypes = $this->avgValueByTypes($typesCalculation); // [art => 65]
        // типы с наибольшими значениями
        $bestTypes = $this->filterTopTypes($avgValueByTypes); // [art => 65]

        $professions = $this->getProfessions();
        $professions = $this->scoreProfessions($professions, $bestTypes);
        // оставим топ подходящих профессий
        $professions = array_slice($professions, 0, self::MAXIMUM_PROFESSIONS_NUMBER);

        return [
            'professions' => $professions,
            'types_descriptions' => $this->typesDescriptions($typesCalculation),
            'types_top' => $bestTypes, // being used in trial report
        ];
    }

    public function calculateUserTypes(): CalculationTypesValues
    {
        $result = new CalculationTypesValues();

        foreach (TypesCombination::ALL as $name) {
            $result->add($name, new Values(
                $this->calculateUserType("{$name}-force"),
                $this->calculateUserType("{$name}-interest"),
                $this->calculateUserType("{$name}-skills")
            ));
        }

        return $result;
    }

    /**
     * Высчитывает сумму положительных и правильных ответов для группы вопросов
     * @param string $groupName e.g. tech-skills
     * @return float
     */
    private function calculateUserType(string $groupName): float
    {
        $questions = $this->questionsHolder->group($groupName);
        $count = count($questions);
        $rightSum = AnswersUtil::sum(new QuestionsHolder($questions), $this->answersHolder);
        $this->applyExtraAnswers($groupName, $this->answersHolder, $rightSum, $count);

        return (round($rightSum / $count * 100));
    }

    /**
     * Среднее значение по типу
     * @param CalculationTypesValues $typesValuesCalculation
     * @return array ['tech' => 34.3, 'body' => 16.6, 'human' => 40]
     */
    public function avgValueByTypes(CalculationTypesValues $typesValuesCalculation): array
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
     * Рассчитывает счёт профессии с использованием набранных значений
     *
     * @param Profession[] $professions
     * @param array $topTypes ['tech' => 20, 'body' => 50, 'human' => 0]
     * @return Profession[] with added scores
     */
    public function scoreProfessions(array $professions, array $topTypes): array
    {
        $calculator = new ProfessionTypeScoreCalculator($topTypes);
        foreach ($professions as $i => $profession) {
            $score = $calculator->calculate($profession->types(), $profession->typesNot());
            $profession->setRating($score);
        }

        // отсортируем профессии по очкам
        usort($professions, function (Profession $a, Profession $b) {
            return $b->getRating() <=> $a->getRating();
        });

        return $professions;
    }

    /*
     * Выделяет наиболее высокие типы
     * ['tech' => 20, 'body' => 50, 'human' => 0, 'craft' => 40]
     * =>
     * ['body' => 50, 'craft' => 40]
     */
    public function filterTopTypes(array $values): array
    {
        arsort($values); // сортируем

        $max = reset($values);
        $min = $max - $max / 1.5; // такая вот формула из головы
        // скорее всего нужно сложить все, взять медиану - всё что выше - топ
//        $min = array_slice($values, 6, 1); // todo протестировать с медианой вместо $min = $max - $max / 1.5


        $limit = 4; // а почему 4?
        $top = [];
        foreach ($values as $name => $value) {
            if ($value >= $max - $min) { // интересная формула. почему так?
                $top[$name] = $value;
            }
            if (count($top) >= $limit) {
                break;
            }
        }

        return $top;
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

    /**
     * Применяет дополнительные правила подсчета
     * @param string $groupName
     * @param AnswersHolder $answersHolder
     * @param int $rightSum
     * @param int $count
     */
    private function applyExtraAnswers(string $groupName, AnswersHolder $answersHolder, int &$rightSum, int &$count)
    {
        if ($groupName == 'it-force') {
            $questionIds = [102];

            foreach ($questionIds as $questionId) {
                $rightSum += $answersHolder->getValuesSum($questionId);
            }
            $count += count($questionIds);
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