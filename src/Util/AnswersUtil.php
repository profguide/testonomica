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
     * Counts sum of answers with numeric and correct values
     * @param array $questions
     * @param AnswersHolder $answersHolder
     * @return int
     */
    public static function sum(array $questions, AnswersHolder $answersHolder): int
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
     * Counts percentage of repeated values and builds map
     * @param AnswersHolder $answersHolder ['1' => 'yes', '2' => 'yes', 3 => 'no']
     * @param int $max - what is 100% value
     * @return array e.g. ['yes' => 100, 'no' => 50]
     */
    public static function percentage(AnswersHolder $answersHolder, int $max): array
    {
        // 1. sum values at first
        $valuesSums = AnswersUtil::sumValuesMap($answersHolder);
        // 2. count percentages
        return AnswersUtil::percentageOfSet($valuesSums, $max);
    }

    /**
     * Counts percentage of repeated values and returns double map with percentage and sum of repeated values
     * e.g. ['1' => 'yes', '2' => 'yes', 3 => 'no']
     * @param AnswersHolder $answersHolder
     * @param int $max - what is 100% value
     * @return array e.g. ['yes' => ['value' => 2, 'percentage' => 100], 'no' => ['value' => 1, 'percentage' => 50]]
     */
    public static function percentageWithValues(AnswersHolder $answersHolder, int $max): array
    {
        $newMap = [];
        $valuesSums = AnswersUtil::sumValuesMap($answersHolder);
        $percentageMap = AnswersUtil::percentageOfSet($valuesSums, $max);
        foreach ($valuesSums as $name => $value) {
            $newMap[$name] = [
                'value' => $value,
                'percentage' => $percentageMap[$name]
            ];
        }
        return $newMap;
    }

    /**
     * Counts sum of repeated values
     * e.g. ['1' => 'yes', '2' => 'yes', 3 => 'no']
     * In this case 'yes' repeats 2 times, and 'no' - once.
     * @param AnswersHolder $answersHolder
     * @return array ['yes' => 2, 'no' => 1]
     */
    public static function sumValuesMap(AnswersHolder $answersHolder): array
    {
        $map = [];
        foreach ($answersHolder->getAll() as $answer) {
            if (count($answer->getValue()) > 1) {
                throw new \LogicException('This method does not involve range values.');
            }
            $value = $answer->getValue()[0];
            if (!isset($map[$value])) {
                $map[$value] = 0;
            }
            $map[$value] = $map[$value] + 1;
        }
        return $map;
    }

    /**
     * Counts percentage for array
     * @param array $map e.g. ['yes' => 2, 'no' => 1]
     * @param int $max - what is 100% value
     * @return array ['yes' => 100, 'no' => 50]
     */
    public static function percentageOfSet(array $map, int $max): array
    {
        $newMap = [];
        foreach ($map as $name => $value) {
            $newMap[$name] = round($value * 100 / $max);
        }
        return $newMap;
    }

    /**
     * @param array $map ['yes' => ['value' => 2, 'percentage' => 100], 'no' => ['value' => 1, 'percentage' => 50]
     * @return int e.g. 3 as a sum of 2 and 1 in the case above
     */
    public static function sumValuesInDoubleMap(array $map): int
    {
        $sum = 0;
        foreach ($map as $name => $sumMap) {
            $sum += $sumMap['value'];
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

    /**
     * E.g.:
     * Entertainments
     * Sports
     * Communication
     * ...
     * @param Question $question
     * @param Answer $answer
     * @return array
     */
    public static function ratingToTextArray(Question $question, Answer $answer): array
    {
        $valuesSrc = [];
        /**@var Option $option */
        foreach ($question->getOptions() as $option) {
            $valuesSrc[$option->getValue()] = $option->getText();
        }
        $texts = [];
        foreach ($answer->getValue() as $value) {
            $texts[$value] = $valuesSrc[$value];
        }
        return $texts;
    }

    /**
     * Считает сумму максимальных вариантов ответов для указанных вопросов
     * Могло бы пригодиться, чтобы уменьшить человеческий фактор, когда считают вручную
     */
    // public static function questionMaximumValuesSum(array $questions)
}