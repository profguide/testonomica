<?php
/**
 * @author: adavydov
 * @since: 09.12.2020
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Test\AnswersHolder;
use App\Test\Proforientation\Profession;
use App\Test\Proforientation\Types;
use App\Test\QuestionsHolder;
use App\Test\CrawlerUtil;
use App\Util\AnswersUtil;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractProforientationCalculator extends AbstractCalculator
{
    const MAXIMUM_PROFESSIONS = 15;

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
        $typesGroupsPercent = $this->calculateTypesGroups();
        $typesSinglePercent = $this->sumTypesGroups($typesGroupsPercent);
        $professions = $this->grabProfessionsByTypes($typesSinglePercent);
        $professions = $this->sliceProfessions($professions, self::MAXIMUM_PROFESSIONS);
        return [
            'types_group_percent' => $typesGroupsPercent,
            'types_single_percent' => $typesSinglePercent,
            'types_descriptions' => $this->typesDescriptions($typesGroupsPercent),
            'types_top' => $this->grabTopTypes($typesSinglePercent),
            'professions' => $professions
        ];
    }

    /**
     * Из ответов формирует массив с процентами вида ['tech' => [33, 20, 50], 'body' => [0, 50, 0]]
     * @return array
     */
    public function calculateTypesGroups(): array
    {
        $types = ['natural', 'tech', 'human', 'body', 'math', 'it', 'craft', 'art', 'hoz', 'com', 'boss', 'war'];
        $result = [];
        foreach ($types as $type) {
            $result[$type] = $this->calculateTypeGroups($type, $this->answersHolder, $this->questionsHolder);
        }
        return $result;
    }

    /**
     * @param string $groupMainName e.g. tech
     * @param AnswersHolder $answersHolder
     * @param QuestionsHolder $questionsHolder
     * @return array
     */
    protected function calculateTypeGroups(string $groupMainName, AnswersHolder $answersHolder, QuestionsHolder $questionsHolder)
    {
        return [
            $this->calculateTypeGroup($groupMainName . '-force', $answersHolder, $questionsHolder),
            $this->calculateTypeGroup($groupMainName . '-interest', $answersHolder, $questionsHolder),
            $this->calculateTypeGroup($groupMainName . '-skills', $answersHolder, $questionsHolder),
        ];
    }

    /**
     * Высчитывает сумму положительных и правильных ответов для группы вопросов
     * @param string $groupName e.g. tech-skills
     * @param AnswersHolder $answersHolder
     * @param QuestionsHolder $questionsHolder
     * @return float
     */
    private function calculateTypeGroup(string $groupName, AnswersHolder $answersHolder, QuestionsHolder $questionsHolder): float
    {
        $questions = $questionsHolder->group($groupName);
        $count = count($questions);
        $rightSum = AnswersUtil::sum(new QuestionsHolder($questions), $answersHolder);
        $this->applyExtraAnswers($groupName, $answersHolder, $rightSum, $count);
        return (round($rightSum / $count * 100));
    }

    /**
     * Суммирует значения групп типов
     * @param array ['tech' => [33, 20, 50], 'body' => [0, 50, 0], 'human' => [40, 40, 40]]
     * @return array ['tech' => 34.3, 'body' => 16.6, 'human' => 40]
     */
    public function sumTypesGroups(array $typesScored): array
    {
        $result = [];
        foreach ($typesScored as $name => $groups) {
            $result[$name] = round(array_sum($groups) / count($groups));
        }
        arsort($result);
        return $result;
    }

    /**
     * Считает рейтинг профессий, используя набранные типы
     * @param array ['tech' => 20, 'body' => 50, 'human' => 0]
     * @return array ['Архитектор' => 1, 'Бариста' => 0.2]
     */
    public function grabProfessionsByTypes(array $typesScored): array
    {
        return $this->grabProfessionsByTypesCombs($this->grabTopTypes($typesScored));
    }

    /**
     *
     * @param array $topTypes
     * @return array
     */
    public function grabProfessionsByTypesCombs(array $topTypes): array
    {
        $resultProfessions = [];
        foreach ($this->getProfessions() as $profession) {
            // посчитаем рейтинг профессии
            $rating = $this->combsRating($topTypes, $profession);
            // отсекаем профессии с нулевым совпадением
            if ($rating > 0) {
                $profession->setRating($rating);
                $resultProfessions[] = $profession;
            }
        }
        usort($resultProfessions, function ($a, $b) {
            /**@var $a Profession */
            /**@var $b Profession */
            if ($a->getRating() == $b->getRating()) {
                return 0;
            }
            return ($a->getRating() > $b->getRating()) ? -1 : 1;
        });
        return $resultProfessions;
    }

    public function combsRating(array $types, Profession $profession): float
    {
        $max = 0.0;
        foreach ($profession->getCombs() as $comb) {
            if (($combRating = $this->oneCombRating($types, $comb, $profession->getNot())) > $max) {
                $max = $combRating;
            }
        }
        return $max;
    }

    /**
     * @param array $typesScored
     * @param array $typesNeeded
     * @param array $not - не учитывать комбинации, где присутствуют опредённые типы.
     * Пригождается, чтобы отсечь профессии, не требовательные к сложным навыками, когда человек их набрал.
     * Например, слесарь - только body, а человек набрал и body и human и it. Если в профессии указано not="human",
     * то рейтинг будет 0
     * @return float
     */
    public function oneCombRating(array $typesScored, array $typesNeeded, array $not = []): float
    {
        $keysTypesScored = array_keys($typesScored);

        // если не набраны все требуемые типы, то это не подходит
        foreach ($typesNeeded as $typeNeed) {
            if (!in_array($typeNeed, $keysTypesScored)) {
                return 0;
            }
        }

        // если набранный тип указан в $not, профессия не подходит
        foreach (array_keys($typesScored) as $typeScored) {
            if (in_array($typeScored, $not)) {
                return 0;
            }
        }

        // сложим успех набранных типов ($typesScored), которые востребованны (есть в $typesNeeded)
        $rate = 0;
        foreach ($typesScored as $type => $value) {
            if (in_array($type, $typesNeeded)) {
                $rate += $value;
            }
        }

//        // получим среднее, чтобы рейтинг был максимум 100% и приведем к дроби, где 1 - это 100%
//        return $rate / count($typesNeeded) / 100;

        // получим сумму сильных качеств
        return $rate;
    }

    /*
     * Выделяет наиболее высокие типы
     * ['tech' => 20, 'body' => 50, 'human' => 0, 'craft' => 40]
     * =>
     * ['body' => 50, 'craft' => 40]
     */
    public function grabTopTypes(array $values)
    {
        arsort($values); // сортируем
        $maxValue = $values[array_key_first($values)]; // максимальное
        $top = [];
//        $offsetTopValues = 20; // от топа вниз на сколько процентов считаем топом
//        $offsetTopValues = 35; // от топа вниз на сколько процентов считаем топом
        $allowMinValue = $maxValue - $maxValue / 1.5;
        $maxCount = 4;
        foreach ($values as $name => $value) {
//            if ($percent >= $maxValue - $offsetTopValues) {
//                $top[$name] = $percent;
//            }
            if ($value >= $maxValue - $allowMinValue) {
                $top[$name] = $value;
            }
            if (count($top) >= $maxCount) {
                break;
            }
        }
        return $top;
    }

    /**
     * @return Profession[]
     */
    public function getProfessions(): array
    {
        $professions = [];
        $crawler = CrawlerUtil::load($this->getProfessionsFileName());
        foreach ($crawler as $professionNode) {
            $professions[] = $this->mapProfession((new Crawler($professionNode)));
        }
        return $professions;
    }

    // todo phpunit
    private function mapProfession(Crawler $crawler): Profession
    {
        return new Profession(
            $crawler->children('name')->text(),
            $this->parseCombs($crawler->children('combs')),
            $this->parseProfessionNot($crawler),
            $this->parseProfessionDescription($crawler));
    }

    public function parseCombs(Crawler $combs): array
    {
        $arr = [];
        /**@var \DOMElement $comb */
        foreach ($combs->children() as $comb) {
            $arr[] = explode(",", trim($comb->getAttribute('comb')));
        }
        return $arr;
    }

    private function parseProfessionNot(Crawler $crawler)
    {
        $arr = [];
        $not = $crawler->attr('not');
        if (!empty($not)) {
            foreach (explode(",", $not) as $word) {
                $arr[] = trim($word);
            }
        }
        return $arr;
    }

    // если будет отнимать много времени, можно сделать lazy, то есть в Profession передавать Crawler всей профессии
    // а когда надо парсить его и доставать нужные части. Вот для описания пригодилось бы. А парсить надо через хелпер
    // ProforientationProfessionMapper::mapDescription($this->crawler);
    // и вообще можно kind сделать объектом ProfessionDescriptionKind
    private function parseProfessionDescription(Crawler $crawler): array
    {
        $description = [];
        $nodeDescription = $crawler->filterXPath('descendant-or-self::description');
        if ($nodeDescription->count() > 0) {
            $kindNodes = $nodeDescription->filterXPath('descendant-or-self::kind');
            if ($kindNodes->count() > 0) {
                foreach ($kindNodes as $kindNode) {
                    $kindCrawler = new Crawler($kindNode);
                    /**@var DOMElement $tag */
                    $kind = [];
                    foreach ($kindCrawler->children() as $tag) {
                        $kind[$tag->nodeName] = $tag->textContent;
                    }
                    $description[] = $kind;
                }
            }
        }
        return $description;
    }

    private function sliceProfessions($professions, int $limit)
    {
        return array_slice($professions, 0, $limit);
    }

    /**
     * Подбирает текстовые описания всех типов по значениям групп: интересы (среднее от усилий интересов) и способности
     * @param array $typesGroups
     * @return array вида ['tech' => ['interest' => '...', 'skills' => '...'], 'natural' => ...]
     */
    protected function typesDescriptions(array $typesGroups)
    {
        $descriptions = ['interest' => [], 'skills' => []];
        foreach ($typesGroups as $typeName => $groups) {
            $name = Types::name($typeName);
            // интерес - это среднее от force + interest
            $interestValue = ($groups[0] + $groups[1]) / 2;
            $skillValue = $groups[2];
            $descriptions['interest'][$typeName] = [
                'name' => $name,
                'text' => Types::interestText($typeName, $interestValue),
                'level' => [
                    'absolute' => Types::level($interestValue)
                ]];
            $descriptions['skills'][$typeName] = [
                'name' => $name,
                'text' => Types::skillsText($typeName, $skillValue),
                'level' => [
                    'absolute' => Types::level($skillValue)
                ]];
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

    protected abstract function getProfessionsFileName(): string;
}