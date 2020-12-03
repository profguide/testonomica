<?php
/**
 * @author: adavydov
 * @since: 01.11.2020
 */

namespace App\Util;

use App\Test\AnswersHolder;
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
            if ($question->getMethod() == Question::METHOD_OPTION) {
                $sum += $answersHolder->getValuesSum($question->getId());
            } elseif ($question->getMethod() == Question::METHOD_TEXT) {
                if (self::isCorrect($question, $answersHolder)) {
                    $sum += 1;
                }
            } elseif ($question->getMethod() == Question::METHOD_OPTION) {
                // todo
                throw new \RuntimeException('Not supported other methods yet');
            } else {
                throw new \RuntimeException('Unsupportable method');
            }
        }
        return $sum;
    }

    /**
     * Проверяет является ли ответ равным и точным ожидаемым значениям
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
}