<?php

namespace App\Test\Calculator;

use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class AutoCalculator extends AbstractCalculator
{
    function calculate(): array
    {
        /*
         * Вопрос: что такое переменная в условии?
         * - сумма всех значений, это легко - уже существует SUM.
         *
         * А если это сумма определённых значений, то как это указать?
         * Можно во время загрузки формы найти все варианты ответов и добавить их в поле
         * Для простых значений это будет "1", "key", "0";
         * Для групповых значений это будет "tech:key".
         */

        // Общее кол-во вопросов
        $questionsCount = count($this->questionsHolder->getAll());

        // Максимальная сумма числовых значений, которую можно набрать в тесте
        $maxSum = AnswersUtil::max($this->questionsHolder);

        // Набранная сумма числовых значений (correct считается как 1)
        $sum = AnswersUtil::sum($this->questionsHolder, $this->answersHolder);

        // Процент набранных числовых значений от максимальной суммы, которую можно набрать в тесте
        $percentage = round($maxSum > 0 ? $sum * 100 / $maxSum : 0);

        // Мапа вида ['no' => ['sum' => 2, 'percentage' => 50], 'yes' => ['sum' => 1, 'percentage' => 25], ...]
        // Процент для каждого значения считается от максимальной суммы, которую можно набрать
        $integerValuesPercentageWithValues = AnswersUtil::percentageWithValues(
            $this->questionsHolder, $this->answersHolder, $maxSum);

        // Мапа вида ['no' => ['sum' => 2, 'percentage' => 50], 'yes' => ['sum' => 1, 'percentage' => 25], ...]
        // Процент для каждого значения считается от общего кол-ва вопросов
        $stringValuesPercentageWithValues = AnswersUtil::percentageWithValues(
            $this->questionsHolder, $this->answersHolder, $questionsCount);

        // Та же мапа, но без отрицательных значений
        $repeatedNonNegativeValuesPercentageWithValues = AnswersUtil::removeNegativeKeys($stringValuesPercentageWithValues);

        // общая сумма неотрицательных(!) значений, используется в Тест на эмпатические способности
        // подходит для тестов, где есть "неправильные" или "ложные" значения, как в тесте на эмпатические способности
        $nonNegativeValuesSum = AnswersUtil::sumValuesInDoubleMap($repeatedNonNegativeValuesPercentageWithValues);

        // Groups
        $groups = [];
        $groupsSums = AnswersUtil::sumByGroups($this->questionsHolder, $this->answersHolder);
        foreach ($groupsSums as $groupName => $groupSum) {
            $groupMaxSum = AnswersUtil::maxInGroup($this->questionsHolder, $groupName);
            $groups[$groupName] = [
                'SUM' => $groupSum,
                'SCALE' => round($groupMaxSum > 0 ? $groupSum * 100 / $groupMaxSum : 0), // процент от максимума внутри группы
                'SCALE_OF_ALL' => 'TODO', // процент от всех
            ];
        }

        // todo добавить в VALUES значения, которые ни разу не были выбраны

        $output = [
            'SUM' => $sum,
            'SCALE' => $percentage,
            'VALUES' => $integerValuesPercentageWithValues,
            'REPEATS' => $stringValuesPercentageWithValues,
            'NON_NEGATIVE_ANSWER_VALUES_SUM' => $nonNegativeValuesSum,
            'GROUPS' => $groups,
        ];

//        dd($output);

        return $output;
    }
}