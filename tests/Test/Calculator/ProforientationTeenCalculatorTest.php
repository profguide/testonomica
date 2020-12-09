<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Tests\Test\Calculator;

use App\Controller\DevelopController;
use App\Entity\Answer;
use App\Test\AnswersHolder;
use App\Test\Calculator\ProforientationTeenCalculator;
use App\Test\Proforientation\Profession;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class ProforientationTeenCalculatorTest extends KernelTestCase
{
    /**@var ProforientationTeenCalculator */
    protected $calculator;

    public function setUp()
    {
        self::bootKernel();
        /**@var KernelInterface $appKernel */
        $appKernel = self::$container->get(KernelInterface::class);
        $this->calculator = new ProforientationTeenCalculator($appKernel);
    }

    /**
     * Натуральный тест - как его бы видет тестируемый - передача ответов
     * Задействованы все механизмы
     */
    public function testCalculateTypesGroups()
    {
        $answersHolder = $this->constructAnswersHolder([
            1 => [1], 2 => [1], 3 => [1], 4 => [1], 5 => [1], 6 => [1], // natural-force
            10 => [1], 11 => [1], // natural-interest
            // no natural-skills
            100 => [1], 101 => [1], //tech-force;
            102 => [1], // tech-force/it-force
            110 => [1], 111 => [1], 112 => [1], //tech-interest
            120 => [1], 121 => [1], 122 => [1], 123 => [40], //tech-interest

            700 => [1], 701 => [1]
        ]);
//        $time = microtime(true);
        $this->assertEquals([
            'natural' => [100, 50, 0],
            'tech' => [100, 100, 100],
            'human' => [0, 0, 0],
            'body' => [0, 0, 0],
            'math' => [0, 0, 0],
            'it' => [33, 0, 0], // one question of tech is also used in it
            'craft' => [0, 0, 0],
            'art' => [67, 0, 0],
            'hoz' => [0, 0, 0],
            'com' => [0, 0, 0],
            'boss' => [0, 0, 0],
            'war' => [0, 0, 0],
        ], $this->calculator->calculateTypesGroups($answersHolder));
//        dd(microtime(true) - $time);
    }

    public function testSummationTypesGroups()
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

    public function testGrabTopTypes()
    {
        $result = $this->calculator->grabTopTypes([
            'natural' => 10,
            'tech' => 50,
            'human' => 40,
            'it' => 30,
            'body' => 0,
        ]);
        $this->assertEquals(['tech' => 50, 'human' => 40], $result);
    }

    public function testRatingCalculationForOneComb()
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
        // есть в аргументе not - 0
        $this->assertEquals(0, $this->calculator->oneCombRating(
            ['natural' => 100, 'war' => 90], ['natural'], ['war']));
    }

    public function testRatingCalculationForAllCombs()
    {
        // Один вариант со 100% совпадением - это 1
        $this->assertEquals(200, $this->calculator->combsRating(
            ['natural' => 100, 'tech' => 100], new Profession('some', [['natural', 'tech']])));
        // Два варианта, один 100%, другой 0 - это 1
        $this->assertEquals(200, $this->calculator->combsRating(
            ['natural' => 100, 'tech' => 100], new Profession('some', [['natural', 'tech'], ['natural', 'tech', 'body']])));
        // Два варианта, один 0 и другой 0 - это 0
        $this->assertEquals(0, $this->calculator->combsRating(
            ['natural' => 100], new Profession('some', [['natural', 'tech'], ['natural', 'tech', 'body']])));
    }

//    /**
//     * Убедиться, что не существует комбинаций, которые не дают ни одной профессии
//     * Закомментированно, потому что очень медленно работает
//     */
//    public function testThatNoCombsWithEmptyProfessionList()
//    {
//        // используем заранее скомпилированный список комбинаций без повторов
//        foreach (DevelopController::COMBS_POSSIBLE as $comb) {
//            $types = array_flip(explode(',', $comb));
//            // поставим 100% для всех типов, чтобы точно все учлись
//            $types = array_fill_keys(array_keys($types), 100);
//            $professions = $this->calculator->grabProfessionsByTypesCombs($types);
//            $this->assertNotEmpty($professions, "Combination \"$comb\" does not have any profession");
//        }
//    }

//    /**
//     * Песочница, чтобы проверить соответствие комбинаций и профессий.
//     * Закомментированно, потому что по сути не является unit-тестом.
//     * Это скорее подошло бы для тестирования через веб-интерфейс.
//     * todo перенести в http, и вызывать оттуда. Например, DevController::proforientation2Types(?types=rand|tech,math),
//     */
//    public function testRandomComb()
//    {
//        $key = array_rand(DevelopController::COMBS_POSSIBLE);
//        $comb = DevelopController::COMBS_POSSIBLE[$key];
//        echo "\nRandom combination: $comb\n";
//        $types = array_flip(explode(',', $comb));
//        // поставим 100% для всех типов, чтобы точно все учлись
//        $types = array_fill_keys(array_keys($types), 100);
//        $professions = $this->calculator->grabProfessionsByTypesCombs($types);
//        $this->assertNotEmpty($professions, "Combination \"$comb\" does not have any profession");
//        foreach ($professions as $name => $value) {
//            echo $name . ': ' . $value . "\n";
//        }
//    }

//    /*
//     * Промежуточный результат подсчёта (проценты) приводим к списку профессий, чтобы играть с настройками
//     */
//    public function testCalculateByTypesPercent()
//    {
//        $result = $this->calculator->grabProfessionsByTypes([
//            'natural' => 10,
//            'tech' => 0,
//            'human' => 0,
//            'body' => 20,
//            'math' => 10,
//            'it' => 0,
//            'craft' => 0,
//            'art' => 0,
//            'hoz' => 0,
//            'com' => 0,
//            'boss' => 0,
//            'war' => 0,
//        ]);
//        echo "\nРейтинг профессий: \n-----\n";
//        foreach ($result as $name => $rate) {
//            echo $name . ": " . $rate . "\n";
//        }
//        $this->assertEquals(1, 1);
//    }

    private function constructAnswersHolder(array $array): AnswersHolder
    {
        $answers = [];
        foreach ($array as $id => $value) {
            $answers[$id] = Answer::create($id, $value);
        }
        return new AnswersHolder($answers);
    }
}