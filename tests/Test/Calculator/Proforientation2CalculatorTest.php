<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Tests\Test\Calculator;

use App\Entity\Answer;
use App\Test\AnswersHolder;
use App\Test\Calculator\Proforientation2Calculator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class Proforientation2CalculatorTest extends KernelTestCase
{
    /**@var Proforientation2Calculator */
    private $calculator;

    public function setUp()
    {
        self::bootKernel();
        $appKernel = self::$container->get(KernelInterface::class);
        $this->calculator = new Proforientation2Calculator($appKernel);
    }

    /**
     * Натуральный тест - как его бы видет тестируемый - передача ответов
     * Задействованы все механизмы
     */
    public function testCalculateTypesGroups()
    {
        $answersHolder = $this->constructAnswersHolder([
            1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, // natural-force
            10 => 1, 11 => 1, // natural-interest
            // no natural-skills
            100 => 1, 101 => 1, 102 => 1, //tech-force
            110 => 1, 111 => 1, 112 => 1, //tech-interest
            120 => 1, 121 => 1, 122 => 1, 123 => 40, //tech-interest

            700 => 1, 701 => 1

        ]);
//        $time = microtime(true);
        $this->assertEquals([
            'natural' => [100, 50, 0],
            'tech' => [100, 100, 100],
            'human' => [0, 0, 0],
            'body' => [0, 0, 0],
            'math' => [0, 0, 0],
            'it' => [0, 0, 0],
            'craft' => [0, 0, 0],
            'art' => [67, 0, 0],
            'hoz' => [0, 0, 0],
            'com' => [0, 0, 0],
            'boss' => [0, 0, 0],
            'war' => [0, 0, 0],
        ], $this->calculator->calculateTypesGroups($answersHolder));
//        dd(microtime(true) - $time);
    }

    /*
     * Промежуточный результат подсчёта (проценты) приводим к списку профессий, чтобы играть с настройками
     */
    public function testCalculateByTypesPercent()
    {
        $result = $this->calculator->stepCalculateByTypesPercent([
            'natural' => 10,
            'tech' => 0,
            'human' => 0,
            'body' => 20,
            'math' => 10,
            'it' => 0,
            'craft' => 0,
            'art' => 0,
            'hoz' => 0,
            'com' => 0,
            'boss' => 0,
            'war' => 0,
        ]);
        echo "\nРейтинг профессий: \n-----\n";
        foreach ($result as $name => $rate) {
            echo $name . ": " . $rate . "\n";
        }
        $this->assertEquals(1, 1);
    }

    public function testSupTypesGroups()
    {
        $types = [
            'natural' => [40, 40, 40],
            'tech' => [20, 40, 60],
            'body' => [0, 0, 0],
        ];
        $this->assertEquals([
            'natural' => 40,
            'tech' => 40,
            'body' => 0
        ], $this->calculator->sumTypesGroups($types));
    }

    public function testFilterTopTypes()
    {
        $result = $this->calculator->filterTopTypes([
            'natural' => 10,
            'tech' => 50,
            'human' => 40,
            'it' => 30,
            'body' => 0,
        ]);
        $this->assertEquals(['tech' => 50, 'human' => 40], $result);
    }

    public function testOneCombsRating()
    {
//         чего-то не хватает - это рейтинг 0
        $this->assertEquals(0, $this->calculator->oneCombRating(
            ['natural' => 100], ['natural', 'tech']));
        // полное совпадение - рейтинг 1
        $this->assertEquals(200, $this->calculator->oneCombRating(
            ['natural' => 100, 'tech' => 100], ['natural', 'tech']));
//        // среднее - 0.5
        $this->assertEquals(100, $this->calculator->oneCombRating(
            ['natural' => 100, 'tech' => 0], ['natural', 'tech']));
        // лишнее - не считаем
        $this->assertEquals(100, $this->calculator->oneCombRating(
            ['natural' => 100, 'tech' => 0, 'body' => 100], ['natural', 'tech']));
    }

    public function testAllCombsRating()
    {
        // Один вариант со 100% совпадением - это 1
        $this->assertEquals(200, $this->calculator->combsRating(
            ['natural' => 100, 'tech' => 100], [['natural', 'tech']]));
        // Два варианта, один 100%, другой 0 - это 1
        $this->assertEquals(200, $this->calculator->combsRating(
            ['natural' => 100, 'tech' => 100], [['natural', 'tech'], ['natural', 'tech', 'body']]));
        // Два варианта, один 0 и другой 0 - это 0
        $this->assertEquals(0, $this->calculator->combsRating(
            ['natural' => 100], [['natural', 'tech'], ['natural', 'tech', 'body']]));
    }

    private function constructAnswersHolder(array $array): AnswersHolder
    {
        $answers = [];
        foreach ($array as $id => $value) {
            $answers[] = Answer::create($id, $value);
        }
        return new AnswersHolder($answers);
    }
}