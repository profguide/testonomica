<?php

namespace App\Test\Calculator;

use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class AutoCalculator extends AbstractCalculator
{
    function calculate(): array
    {
        /*
         * todo Конструктор результатов для чайников
         * Структура:
         *  Analysis
         *  - test
         *  - title
         *  - scaleVariableName
         *  - scalePercentVariableName
         *  - scaleVariableMax
         *  - AnalysisBlock:
         *      - conditions:
         *          - AnalysisCondition
         *      - text
         *
         * Нажать кнопку "Добавить вариант результата".
         *  Создастся блок с элементами:
         *  - textarea текст варианта
         *  - select переменная для шкалы (Sopernichestvo, SUM, )
         *  - button добавить условие
         *
         * Нажать кнопку "добавить условие"
         *  Добавится блок с элементами:
         *  - переменная
         *  - условие (больше/меньше/равно)
         *  - референтное значение
         *
         * Нажать кнопку "добавить еще одно условие"
         *  Добавится блок с элементами:
         *  - и/или/не
         *  - переменная
         *  - условие (больше/меньше/равно)
         *  - референтное значение
         *
         * Поле resultView так же идёт в счёт. Его содержимое отображается под конструктором результатов.
         * resultView отображается даже если конструктора нет. Его можно использовать для более сложных вариантов результатов с таблицами.
         * Либо для пояснения результатов и для рекламы.
         *
         * Вопрос: что такое переменная в условии?
         * - сумма всех значений, это легко - уже существует SUM.
         *
         * А если это сумма определённых значений, то как это указать?
         * Можно во время загрузки формы найти все варианты ответов и добавить их в поле
         * Для простых значений это будет "1", "key", "0";
         * Для групповых значений это будет "tech:key".
         *
         *
         */

        // Общее кол-во вопросов
        $questionsCount = count($this->questionsHolder->getAll());

        // Максимальная сумма числовых значений, которую можно набрать в тесте
        $maxSum = AnswersUtil::max($this->questionsHolder);

        // Набранная сумма числовых значений (correct считается как 1)
        $sum = AnswersUtil::sum($this->questionsHolder, $this->answersHolder);

        // Процент набранных числовых значений от максимальной суммы, которую можно набрать в тесте
        $percentage = $maxSum > 0 ? $sum * 100 / $maxSum : 0;

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

        return [
            'SUM' => $sum,
            'SCALE' => $percentage,
            'VALUES' => $integerValuesPercentageWithValues,
            'REPEATS' => $stringValuesPercentageWithValues,
            'NON_NEGATIVE_ANSWER_VALUES_SUM' => $nonNegativeValuesSum,
        ];
    }
}