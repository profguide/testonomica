<?php
/**
 * @author: adavydov
 * @since: 01.11.2020
 */

namespace App\Util;

use App\Entity\Answer;
use App\Test\AnswersHolder;
use App\Test\Option;
use App\Test\Question;

class AnswersUtil
{
    /**
     * todo write description and how to use
     * @param array $questions
     * @param AnswersHolder $answersHolder
     * @return int
     */
    public static function sum(array $questions, AnswersHolder $answersHolder)
    {
        $sum = 0;
        /**@var Question $question */
        foreach ($questions as $question) {
            // if the question assumes correct answer and the answer is correct, it will add "1"
            if ($question->hasCorrectValues()) {
                if (self::isCorrect($question, $answersHolder)) {
                    $sum += 1;
                }
            } else {
                $sum += $answersHolder->getValuesSum($question->getId());
            }
        }
        return $sum;
    }

    /**
     * Checks if all answer's values match question's expected ones
     * @param Question $question
     * @param AnswersHolder $answersHolder
     * @return bool
     */
    private static function isCorrect(Question $question, AnswersHolder $answersHolder): bool
    {
        $correctValues = $question->getCorrectValues();
        $scoredValues = $answersHolder->getValues($question->getId());
        return empty(array_merge(
            array_diff($scoredValues, $correctValues),
            array_diff($correctValues, $scoredValues)
        ));
    }

    public static function ratingToTextArray(Question $question, Answer $answer)
    {
        $valuesSrc = [];
        /**@var Option $option */
        foreach ($question->getOptions() as $option) {
            $valuesSrc[$option->getValue()] = $option->getText();
        }
        $texts = [];
        foreach ($answer->getValue() as $value) {
            $texts[] = $valuesSrc[$value];
        }
        return $texts;
    }

    /**
     * Считает сумму максимальных вариантов ответов для указанных вопросов
     * Могло бы пригодиться, чтобы уменьшить человеческий фактор, когда считают вручную
     */
    // public static function questionMaximumValuesSum(array $questions)
}