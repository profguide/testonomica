<?php
/**
 * @author: adavydov
 * @since: 11.12.2020
 */

namespace App\Tests\Test\Calculator;


use App\Entity\Answer;
use App\Subscriber\Locale;
use App\Test\AnswersHolder;
use App\Test\Calculator\AbstractProforientationCalculator;
use App\Test\CrawlerUtil;
use App\Test\Proforientation\Profession;
use App\Test\Proforientation\ValueSystem;
use App\Test\QuestionsHolder;
use App\Test\QuestionXmlMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractProforientationCalculatorTest extends KernelTestCase
{
    /**@var string */
    protected $calculatorName;

    /**
     * Натуральный тест - как его видит тестируемый - передача ответов
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
            120 => [1], 121 => [1], 122 => [1], 123 => [40], //tech-skills

            700 => [1], 701 => [1]
        ]);

        /**@var AbstractProforientationCalculator $calculator */
        $calculator = new $this->calculatorName($answersHolder, $this->questionsHolder(), self::$kernel);

        $this->assertEquals([
            'natural' => [100.0, 50, 0],
            'tech' => [100.0, 100.0, 100.0],
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
        ], $calculator->calculateUserTypes());
    }

    public function testSummationTypesGroups()
    {
        $calculator = $this->createEmptyCalculator();
        $types = [
            'natural' => [40, 40, 40],
            'tech' => [20, 40, 60],
            'body' => [0, 0, 0],
        ];
        $this->assertEquals([
            'natural' => 40,
            'tech' => 40,
            'body' => 0
        ], $calculator->avgValueByTypes($types));
    }

    public function testGrabTopTypes()
    {
        $calculator = $this->createEmptyCalculator();
        $result = $calculator->filterTopTypes([
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
        $calculator = $this->createEmptyCalculator();
//         чего-то не хватает - это рейтинг 0
        $this->assertEquals(0, $calculator->oneCombRating(
            ['natural' => 100], ['natural', 'tech']));
        // полное совпадение - рейтинг 1
        $this->assertEquals(200, $calculator->oneCombRating(
            ['natural' => 100, 'tech' => 100], ['natural', 'tech']));
//        // среднее - 0.5
        $this->assertEquals(100, $calculator->oneCombRating(
            ['natural' => 100, 'tech' => 0], ['natural', 'tech']));
        // лишнее - не считаем
        $this->assertEquals(100, $calculator->oneCombRating(
            ['natural' => 100, 'tech' => 0, 'body' => 100], ['natural', 'tech']));
        // есть в аргументе not - 0
        $this->assertEquals(0, $calculator->oneCombRating(
            ['natural' => 100, 'war' => 90], ['natural'], ['war']));
    }

    public function testRatingCalculationForAllCombs()
    {
        $calculator = $this->createEmptyCalculator();
        // Один вариант со 100% совпадением - это 1
        $this->assertEquals(200, $calculator->combsRating(
            ['natural' => 100, 'tech' => 100], new Profession('some', [['natural', 'tech']], new ValueSystem([]))));
        // Два варианта, один 100%, другой 0 - это 1
        $this->assertEquals(200, $calculator->combsRating(
            ['natural' => 100, 'tech' => 100], new Profession('some', [['natural', 'tech'], ['natural', 'tech', 'body']], new ValueSystem([]))));
        // Два варианта, один 0 и другой 0 - это 0
        $this->assertEquals(0, $calculator->combsRating(
            ['natural' => 100], new Profession('some', [['natural', 'tech'], ['natural', 'tech', 'body']], new ValueSystem([]))));
    }

//    public function testFitVersionV201()
//    {
//        /**@var AbstractProforientationCalculator $calculator */
//        $calculator = new $this->calculatorName(new AnswersHolder([
//            720 => new Answer(720, [1]),
//            721 => new Answer(721, [1]),
//            722 => new Answer(722, [1]),
//            // << absent 723 answer obliged Calculator to ignore 723 question
//        ]), $this->questionsHolder(), self::$kernel);
//        $this->assertEquals(100, $calculator->calculate()['types_group_percent']['art'][2]);
//    }

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
            $answers[$id] = new Answer($id, $value);
        }
        return new AnswersHolder($answers);
    }

    private function questionsHolder(): QuestionsHolder
    {
        $crawler = CrawlerUtil::load($this->getSrcFilename());
        $questions = [];
        foreach ($crawler->children() as $node) {
            $question = QuestionXmlMapper::map($node, new Locale(new RequestStack()));
            $questions[$question->getId()] = $question;
        }
        return new QuestionsHolder($questions);
    }

    protected abstract function getSrcFilename(): string;

    private function createEmptyCalculator(): AbstractProforientationCalculator
    {
        return new $this->calculatorName(new AnswersHolder([]), new QuestionsHolder([]), self::$kernel);
    }
}