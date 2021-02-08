<?php
/**
 * @author: adavydov
 * @since: 10.12.2020
 */

namespace App\Tests\Util;


use App\Entity\Answer;
use App\Test\AnswersHolder;
use App\Test\Field;
use App\Test\Option;
use App\Test\Question;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnswersUtilTest extends KernelTestCase
{
    public function setUp()
    {
        self::bootKernel();
    }

    /**
     * Сумма значений с методом OPTION.
     * При этом OPTION может иметь "правильные ответы", а может не иметь.
     * Если вопрос предполагает "правильный ответ", и ответ правильный, то это должно прибавить единицу.
     * А если вопрос не предполагает "правильного ответа", то прибавляется значение ответа.
     */
    public function testSumOptions()
    {
        $questions = self::buildQuestions([
            1 => [0 => 'correct', 1, 2], // with correct
            2 => [0, 1 => 'correct', 2], // with correct
            3 => [0, 1, 2 => 'correct'], // with correct
            4 => [0, 1, 2] // with value
        ]);
        $answers = self::buildAnswers([
            1 => 0, // right + 1
            2 => 1, // right + 1
            3 => 0, // wrong + 0
            4 => 2 // value + 2
        ]);
        $this->assertEquals(4, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder($answers)));
    }

    /**
     * Сумма значений с методом TEXT.
     * TEXT всегда предполагает правильный ответ.
     * Правильным будет считаться точное совпадение всех текстовых полей вопроса и ответа
     */
    public function testSumText()
    {
        $question = new Question();
        $question->setId(1);
        $question->setMethod(Question::METHOD_TEXT);
        $question->addField(new Field('string', null, '16'));
        $question->addField(new Field('string', null, '20'));
        $questions = [$question];

        $this->assertEquals(0, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => 16
        ]))));

        $this->assertEquals(1, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [16, 20]
        ]))));
    }

    /*
     * Мап сумм значений, где ключ - значение
     */
    public function testSumValuesMap()
    {
        $this->assertEquals([
            'yes' => 2,
            'no' => 1
        ], AnswersUtil::sumValuesMap(new AnswersHolder(self::buildAnswers([
            1 => 'yes',
            2 => 'yes',
            3 => 'no',
        ]))));
    }

    /*
     * Мама процентов от значений
     */
    public function testPercentage()
    {
        $this->assertEquals([
            'yes' => 100,
            'no' => 50
        ], AnswersUtil::percentage(new AnswersHolder(self::buildAnswers([
            1 => 'yes',
            2 => 'yes',
            3 => 'no',
        ])), 2));
    }

    /*
     * Мапа процентов от значений
     */
    public function testPercentageOfSet()
    {
        $this->assertEquals([
            'yes' => 100,
            'no' => 50
        ], AnswersUtil::percentageOfSet([
            'yes' => 2,
            'no' => 1
        ], 2));
    }

    /*
     * Мапа вида ['значение' => ['value' => 'сумма', 'percentage' => 'процент']...]
     */
    public function testPercentageWithValues()
    {
        $this->assertEquals([
            'yes' => [
                'value' => 2,
                'percentage' => 100
            ],
            'no' => [
                'value' => 1,
                'percentage' => 50
            ]
        ], AnswersUtil::percentageWithValues(new AnswersHolder(self::buildAnswers([
            1 => 'yes',
            2 => 'yes',
            3 => 'no',
        ])), 2));
    }

    private static function buildQuestions(array $array): array
    {
        $questions = [];
        foreach ($array as $id => $values) {
            $questions[] = self::createQuestionWithOptions($id, $values);
        }
        return $questions;
    }

    private static function buildAnswers(array $array): array
    {
        $answers = [];
        foreach ($array as $id => $value) {
            $answers[$id] = self::createAnswer($id, $value);
        }
        return $answers;
    }

    private static function createAnswer($id, $value): Answer
    {
        $value = is_array($value) ? $value : [$value];
        return Answer::create($id, $value);
    }

    /**
     * @param int $id
     * @param $options [0, 1, 2] or [0, 1 => 'correct', 2]
     * @return Question
     */
    private static function createQuestionWithOptions(int $id, array $options): Question
    {
        $q = new Question();
        $q->setId($id);
        $q->setMethod(Question::METHOD_OPTION);
        $o = [];
        foreach ($options as $k => $v) {
            if ($v === 'correct') {
                $o[] = new Option($k, true, null);
            } else {
                $o[] = new Option($v, false, null);
            }
        }
        $q->setOptions($o);
        return $q;
    }

}