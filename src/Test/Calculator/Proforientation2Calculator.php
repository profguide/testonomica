<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Test\Calculator;


use App\Test\AnswersHolder;
use App\Test\CalculatorInterface;
use App\Test\QuestionsHolder;
use App\Test\QuestionXmlMapper;
use App\Test\CrawlerUtil;
use App\Util\AnswersUtil;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

class Proforientation2Calculator implements CalculatorInterface
{
    /**@var KernelInterface */
    private $kernel;

    /**@var QuestionsHolder */
    private $questions;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->questions = $this->loadQuestions();
    }

    public function calculate(AnswersHolder $answersHolder): array
    {
        $typesGroupsPercent = $this->calculateTypesGroups($answersHolder);
        $typesSinglePercent = $this->sumTypesGroups($typesGroupsPercent);
        $professions = $this->stepCalculateByTypesPercent($typesSinglePercent);
        return [
            'types_group_percent' => $typesGroupsPercent,
            'types_single_percent' => $typesSinglePercent,
            'types_top' => $this->filterTopTypes($typesSinglePercent),
            'professions' => $professions
        ];
    }

    /**
     * Из ответов формирует массив с процентами вида ['tech' => [33, 20, 50], 'body' => [0, 50, 0]]
     * @param AnswersHolder $answersHolder
     * @return array
     */
    public function calculateTypesGroups(AnswersHolder $answersHolder): array
    {
        $types = ['natural', 'tech', 'human', 'body', 'math', 'it', 'craft', 'art', 'hoz', 'com', 'boss', 'war'];
        $result = [];
        foreach ($types as $type) {
            $result[$type] = $this->calculateTypeGroups($type, $answersHolder);
        }
        return $result;
    }

    private function calculateTypeGroups(string $groupMainName, AnswersHolder $answersHolder)
    {
        return [
            $this->calculateTypeGroup($groupMainName . '-force', $answersHolder),
            $this->calculateTypeGroup($groupMainName . '-interest', $answersHolder),
            $this->calculateTypeGroup($groupMainName . '-skills', $answersHolder),
        ];
    }

    private function calculateTypeGroup(string $groupName, AnswersHolder $answersHolder)
    {
        $questions = $this->questions->group($groupName);
        $count = count($questions);
        $rightSum = AnswersUtil::sum($questions, $answersHolder);
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
        return $result;
    }

    /**
     * Считает рейтинг профессий, используя набранные типы
     * todo pass Crawler with professions. Будет удобно тестировать отдельные профессии
     * @param array ['tech' => 20, 'body' => 50, 'human' => 0]
     * @return array ['Архитектор' => 1, 'Бариста' => 0.2]
     */
    public function stepCalculateByTypesPercent(array $typesScored): array
    {
        $resultProfessions = [];
        $topTypes = $this->filterTopTypes($typesScored); // топовые типы
        //
        foreach ($this->loadProfessionsCrawler() as $professionNode) {
            $professionCrawler = (new Crawler($professionNode));
            // переведем xml комбинации профессии в список вида [['art', 'body'], ['body']]
            $typesNeeded = $this->parseCombs($professionCrawler->children('combs'));
            // посчитаем рейтинг профессии
            $rating = $this->combsRating($topTypes, $typesNeeded);
            // отсекаем профессии с нулевым совпадением
            if ($rating > 0) {
                $name = $professionCrawler->children('name')->text();
                $resultProfessions[$name] = $rating;
            }
        }
        arsort($resultProfessions);
        return $resultProfessions;
    }

    public function combsRating(array $types, array $combs): float
    {
        $max = 0.0;
        foreach ($combs as $comb) {
            if (($combRating = $this->oneCombRating($types, $comb)) > $max) {
                $max = $combRating;
            }
        }
        return $max;
    }

    public function oneCombRating(array $typesScored, array $typesNeeded): float
    {
        $keysTypesScored = array_keys($typesScored);

        // если не набраны все требуемые типы, то это не подходит
        foreach ($typesNeeded as $typeNeed) {
            if (!in_array($typeNeed, $keysTypesScored)) {
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

        // получим среднее, чтобы рейтинг был максимум 100% и приведем к дроби, где 1 - это 100%
        return $rate / count($typesNeeded) / 100;
    }

    /*
     * Выделяет наиболее высокие типы
     * ['tech' => 20, 'body' => 50, 'human' => 0, 'craft' => 40]
     * =>
     * ['body' => 50, 'craft' => 40]
     */
    public function filterTopTypes(array $values)
    {
        arsort($values); // сортируем
        $maxValue = $values[array_key_first($values)]; // максимальное
        $top = [];
        $offsetTopValues = 25; // от топа вниз на сколько процентов считаем топом
        $maxCount = 4;
        foreach ($values as $name => $percent) {
            // примитивная логика: берём все максимумы и то, что чуть до максимума не добрали
            if ($percent >= $maxValue - $offsetTopValues) {
                $top[$name] = $percent;
            }
            if (count($top) >= $maxCount) {
                break;
            }
        }
        return $top;
    }

    // todo phpunit
    public function parseCombs(Crawler $combs): array
    {
        $arr = [];
        /**@var \DOMElement $comb */
        foreach ($combs->children() as $comb) {
            $arr[] = explode(",", trim($comb->getAttribute('comb')));
        }
        return $arr;
    }

    public function loadProfessionsCrawler(): Crawler
    {
        $professionsXmlFileName = $this->kernel->getProjectDir() . "/xml/proforientation2Professions.xml";
        $crawler = new Crawler(file_get_contents($professionsXmlFileName));
        return $crawler->children();
    }

    private function loadQuestions()
    {
        $items = CrawlerUtil::load($this->kernel->getProjectDir() . "/xml/proforientation2.xml");
        $questions = [];
        /**@var \DOMElement $item */
        foreach ($items->children() as $item) {
            $questions[] = QuestionXmlMapper::map($item);
        }
        return new QuestionsHolder($questions);
    }
}