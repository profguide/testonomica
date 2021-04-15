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
        $max = AnswersUtil::max($this->questionsHolder);

        $sum = AnswersUtil::sum($this->questionsHolder, $this->answersHolder);
        $scale = $sum * 100 / $max;

        $percentageWithValues = AnswersUtil::percentageWithValues($this->questionsHolder, $this->answersHolder, $max);

        return [
            'SUM' => $sum,
            'SCALE' => $scale,
            'VALUES' => $percentageWithValues
        ];
    }
}