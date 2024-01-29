<?php
/**
 * @author: adavydov
 * @since: 10.12.2020
 */

namespace App\Tests\Util;


use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\QuestionItem;
use App\Test\AnswersHolder;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

//use App\Test\Field;
//use App\Test\Option;
//use App\Test\Question;

class AnswersUtilTest extends KernelTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * Проверяем, что сумма значений равна числовому значению выбранного варианта
     */
    public function testSumOptionsIntegerBased()
    {
        $question = new Question();
        $question->setId(1);
        $question->setType(Question::TYPE_OPTION);
        $question->addItem(QuestionItem::createMinimal('1', 'Один'));
        $question->addItem(QuestionItem::createMinimal('2', 'Два'));
        $question->addItem(QuestionItem::createMinimal('3', 'Три'));
        $questions = [$question];

        // сумма ответов - числовое значение
        $this->assertEquals(3, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [3]
        ]))));
    }

    /**
     * Проверяем, что сумма "корректных" значений равна единице
     */
    public function testSumOptionsWithCorrectOption()
    {
        $question = new Question();
        $question->setId(1);
        $question->setType(Question::TYPE_OPTION);
        $question->addItem(QuestionItem::createMinimal('1', 'Один'));
        $question->addItem(QuestionItem::createMinimal('2', 'Два'));
        $question->addItem(QuestionItem::createMinimal('3', 'Три', null, true));
        $questions = [$question];

        // сумма "корректных" ответов равна единице - потому что "корректный" - значит +1
        $this->assertEquals(1, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [3]
        ]))));

        // сумма "некорректных" ответов равна нулю
        $this->assertEquals(0, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [2]
        ]))));
    }

    /**
     * Правильный ответ - любой из корректных
     */
    public function testAnyOptionIsCorrect()
    {
        $question = new Question();
        $question->setId(1);
        $question->setType(Question::TYPE_OPTION);
        $question->addItem(QuestionItem::createMinimal('1', 'Один', null, true));
        $question->addItem(QuestionItem::createMinimal('2', 'Два', null, true));
        $question->addItem(QuestionItem::createMinimal('3', 'Три'));
        $questions = [$question];

        // вариант корректный
        $this->assertEquals(1, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [1]
        ]))));

        // вариант корректный
        $this->assertEquals(1, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [2]
        ]))));

        // вариант не корректный
        $this->assertEquals(0, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [3]
        ]))));
    }

    /**
     * Правильный ответ - ровное число правильных ответов
     */
    public function testStrictAmountOptionsIsCorrect()
    {
        $question = new Question();
        $question->setId(1);
        $question->setCount(2); // << два обязательных
        $question->setType(Question::TYPE_OPTION);
        $question->addItem(QuestionItem::createMinimal('1', 'Один', null, true));
        $question->addItem(QuestionItem::createMinimal('2', 'Два', null, true));
        $question->addItem(QuestionItem::createMinimal('3', 'Три'));
        $questions = [$question];

        // строго оба корректных - правильно
        $this->assertEquals(1, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [1, 2]
        ]))));

        // один из корректных - неправильно
        $this->assertEquals(0, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [1]
        ]))));

        // некорректный - неправильно
        $this->assertEquals(0, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [3]
        ]))));

        // один корректный и один некорректный - неправильно
        $this->assertEquals(0, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [1, 3]
        ]))));

        // оба корректных вместе с некорректным - неправильно
        $this->assertEquals(0, AnswersUtil::sum(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
            1 => [1, 2, 3]
        ]))));
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
        $question->setType(Question::TYPE_TEXT);
        $question->addItem(QuestionItem::createMinimal('16', 'Введите число'));
        $question->addItem(QuestionItem::createMinimal('20', 'Введите число'));
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
        $questions = self::buildQuestions([
            1 => ['yes'],
            2 => ['yes'],
            3 => ['no'],
        ]);
        $this->assertEquals([
            'yes' => 2,
            'no' => 1
        ], AnswersUtil::sumValuesMap(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
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
        $questions = self::buildQuestions([
            1 => ['yes'],
            2 => ['yes'],
            3 => ['no']
        ]);
        $this->assertEquals([
            'yes' => 100,
            'no' => 50
        ], AnswersUtil::percentage(new QuestionsHolder($questions), new AnswersHolder(self::buildAnswers([
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
                'sum' => 2,
                'percentage' => 100,
                'percentage_value' => 100
            ],
            'no' => [
                'sum' => 1,
                'percentage' => 50,
                'percentage_value' => 100
            ]
        ], AnswersUtil::percentageWithValues(new QuestionsHolder(self::buildQuestions([
            // no matter what values are here, but their count and ids must be the same
            1 => ['yes'],
            2 => ['yes'],
            3 => ['no']
        ])
        ), new AnswersHolder(self::buildAnswers([
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
        return new Answer($id, $value);
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
        $q->setType(Question::TYPE_OPTION);
        $o = [];
        foreach ($options as $k => $v) {
            if ($v === 'correct') {
                $o[] = QuestionItem::createMinimal($k, "Вариант", null, true);
            } else {
                $o[] = QuestionItem::createMinimal($v, "Вариант", null, false);
            }
        }
        $q->setItems(new ArrayCollection($o));
        return $q;
    }

}