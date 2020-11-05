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
            $value = $answersHolder->getValue($question->getId()) ?? 0;
            if ($question->getMethod() == Question::METHOD_OPTION) {
                $sum += (int)$value;
            } elseif ($question->getMethod() == Question::METHOD_TEXT) {
                $correctValues = $question->getCorrectValues();
//                if (count($correctValues) == count($value))... когда $value будет массивом
                if (in_array($value, $correctValues)) {
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
}