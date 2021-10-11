<?php
/**
 * @author: adavydov
 * @since: 01.11.2020
 */

namespace App\Util;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\QuestionItem;
use App\Test\AnswersHolder;
use App\Test\QuestionsHolder;

class AnswersUtil
{
    /**
     * Counts sum of answers values.
     * 'Correct' answer means +1, otherwise value considers as it is
     *
     * @param QuestionsHolder $questionsHolder
     * @param AnswersHolder $answersHolder
     * @return int
     */
    public static function sum(QuestionsHolder $questionsHolder, AnswersHolder $answersHolder): int
    {
        $sum = 0;
        foreach ($questionsHolder->getAll() as $question) {
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
     * Counts the maximum sum possible sum of values (only for integer values).
     *
     * @param QuestionsHolder $questionsHolder
     * @return int
     */
    public static function max(QuestionsHolder $questionsHolder): int
    {
        $sum = 0;
        foreach ($questionsHolder->getAll() as $question) {
            $sum += $question->maxValue();
        }
        return $sum;
    }

    /**
     * Counts the maximum sum of repeated values.
     * todo phpunit
     *
     * @param QuestionsHolder $questionsHolder
     * @return int
     */
    public static function maxRepeated(QuestionsHolder $questionsHolder): int
    {
        return max(AnswersUtil::maxRepeatedValues($questionsHolder)) ?? 0;
    }

    /**
     * Counts all maximum sums of every repeated value.
     * todo phpunit
     *
     * @param QuestionsHolder $questionsHolder
     * @return array
     */
    private static function maxRepeatedValues(QuestionsHolder $questionsHolder): array
    {
        $values = [];
        foreach ($questionsHolder->getAll() as $question) {
            /**@var QuestionItem $item */
            foreach ($question->getItems() as $item) {
                if (!isset($values[$item->getValue()])) {
                    $values[$item->getValue()] = 0;
                }
                $values[$item->getValue()]++;
            }
        }
        return $values;
    }

    /**
     * Removes negative values from the set
     * todo: phpunit
     *
     * @param array $map , e.g.: ['-1' => ..., '1' => ..., 'some_string' => ...]
     * @return array, in this case: ['1' => ..., 'some_string' => ...]
     */
    public static function removeNegativeKeys(array $map): array
    {
        $newMap = [];
        foreach ($map as $value => $data) {
            if ((int)$value >= 0) {
                $newMap[$value] = $data;
            }
        }
        return $newMap;
    }

    /**
     * Sums values in groups and returns map of sums by group names.
     * @param QuestionsHolder $questionsHolder
     * @param AnswersHolder $answersHolder e.g.
     * @return array, e.g. ['bmw' => 5, 'opel' => 3];
     */
    public static function sumByGroups(QuestionsHolder $questionsHolder, AnswersHolder $answersHolder): array
    {
        $groups = $questionsHolder->byGroups();
        $sums = [];
        foreach ($groups as $name => $questions) {
            $sums[$name] = AnswersUtil::sum(new QuestionsHolder($questions), $answersHolder);
        }
        return $sums;
    }

    public static function questionsByGroupMask()
    {

    }

    public static function sumByGroupsMask(QuestionsHolder $questionsHolder, AnswersHolder $answersHolder, string $mask = "/(\w+)[-]\d+/"): array
    {
        $sums = [];
        foreach ($questionsHolder->byGroupsMask($mask) as $name => $questions) {
            $sums[$name] = AnswersUtil::sum(new QuestionsHolder($questions), $answersHolder);
        }
        return $sums;
    }

    /**
     * Counts sum of array values
     * @param array $sums ['bmw' => 2, 'mercedes' => 2, 'ford' => 1]. Sum is 5
     * @param array $onlyValues optional, e.g. ['bmw', 'mercedes']. In this case sum is 4. Safe way.
     * If key is not presented in $sums, it will be ignored
     * @return float|int|mixed
     */
    public static function arraySum(array $sums, ...$onlyValues): int
    {
        if (empty($onlyValues)) {
            return array_sum($sums);
        }
        $sum = 0;
        foreach ($sums as $key => $value) {
            if (in_array($key, $onlyValues, true)) {
                $sum += $value;
            }
        }
        return $sum;
    }

    /**
     * Counts percentage of repeated values and builds map
     * @param QuestionsHolder $questionsHolder
     * @param AnswersHolder $answersHolder ['1' => 'yes', '2' => 'yes', 3 => 'no']
     * @param int $max - what is 100% value
     * @return array e.g. ['yes' => 100, 'no' => 50]
     */
    public static function percentage(QuestionsHolder $questionsHolder, AnswersHolder $answersHolder, int $max): array
    {
        // 1. sum values at first
        $valuesSums = AnswersUtil::sumValuesMap($questionsHolder, $answersHolder);
        // 2. count percentages
        return AnswersUtil::percentageOfSet($valuesSums, $max);
    }

    /**
     * Counts percentage of repeated values and returns double map with percentage and sum of repeated values
     * e.g. ['1' => 'yes', '2' => 'yes', 3 => 'no']
     *
     * @param QuestionsHolder $questionsHolder
     * @param AnswersHolder $answersHolder
     * @param int $max - what is 100% value
     * @return array e.g. ['yes' => ['sum' => 2, 'percentage' => 100], 'no' => ['sum' => 1, 'percentage' => 50]]
     */
    public static function percentageWithValues(QuestionsHolder $questionsHolder, AnswersHolder $answersHolder, int $max): array
    {
        $newMap = [];
        $valuesSums = AnswersUtil::sumValuesMap($questionsHolder, $answersHolder);
        // процент значения от максимального для всех значений
        $totalPercentageMap = AnswersUtil::percentageOfSet($valuesSums, $max);
        // процент значения от максимально возможного для этого значения
        $valuesPercentage = AnswersUtil::valuesPercentageOfSet($valuesSums, $questionsHolder);
        foreach ($valuesSums as $name => $sum) {
            $newMap[$name] = [
                'sum' => $sum,
                'percentage' => $totalPercentageMap[$name],
                'percentage_value' => $valuesPercentage[$name]
            ];
        }
        return $newMap;
    }

    /**
     * Counts sum of repeated values
     * e.g. ['1' => 'yes', '2' => 'yes', 3 => 'no']
     * In this case 'yes' repeats 2 times, and 'no' - once.
     * @param QuestionsHolder $questionsHolder need for precise count, as some questions can be passed
     * @param AnswersHolder $answersHolder
     * @return array ['yes' => 2, 'no' => 1]
     */
    public static function sumValuesMap(QuestionsHolder $questionsHolder, AnswersHolder $answersHolder): array
    {
        $map = [];
        foreach ($questionsHolder->getAll() as $question) {
            /**@var QuestionItem $item мапа обязана собрать абсолютно все варианты, даже если они не попали в ответ (бывает) */
            foreach ($question->getItems() as $item) {
                if (!isset($map[$item->getValue()])) {
                    $map[$item->getValue()] = 0;
                }
            }
            $answer = $answersHolder->get($question->getId());
            if (count($answer->getValue()) > 1) { // по-моему устарело, щас все ответы на основе массива. надо проверить
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
     *
     * @param array $map e.g. ['yes' => 2, 'no' => 1]
     * @param int $max - what is 100% value
     * @return array ['yes' => 100, 'no' => 50]
     */
    public static function percentageOfSet(array $map, int $max): array
    {
        $newMap = [];
        foreach ($map as $name => $value) {
            $newMap[$name] = $max > 0 ? round($value * 100 / $max) : 0;
        }
        return $newMap;
    }

    /**
     * @param array $map ['yes' => ['sum' => 2, ...], 'no' => ['sum' => 1, ...]
     * @return int e.g. 3 as a sum of 2 and 1 in the case above
     */
    public static function sumValuesInDoubleMap(array $map): int
    {
        $sum = 0;
        foreach ($map as $name => $sumMap) {
            $sum += $sumMap['sum'];
        }
        return $sum;
    }

    /**
     * Checks if all answer's values match question's expected ones
     *
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
        /**@var QuestionItem $option */
        foreach ($question->getItems() as $option) {
            $valuesSrc[$option->getValue()] = $option->getText();
        }
        $texts = [];
        foreach ($answer->getValue() as $value) {
            $texts[$value] = $valuesSrc[$value];
        }
        return $texts;
    }
//
//    private static function percentageByName(QuestionsHolder $questionsHolder, string $name)
//    {
////        foreach ($questionsHolder->getAll())
//    }

    /**
     * Counts percentage for every value individually
     *
     * @param array $map
     * @param QuestionsHolder $questionsHolder
     * @return array
     */
    private static function valuesPercentageOfSet(array $map, QuestionsHolder $questionsHolder): array
    {
        $percentages = [];
        $maximums = AnswersUtil::maxRepeatedValues($questionsHolder);
        foreach ($maximums as $name => $max) {
            $percentages[$name] = $max > 0 ? round($map[$name] * 100 / $max) : 0;
        }
        return $percentages;
    }
}