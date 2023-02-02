<?php

declare(strict_types=1);

namespace App\Test\Helper;

use App\Test\Proforientation\ValueSystem;

final class ProfessionValueSystemRelevanceCalculator
{
    private array $allValues;

    private array $userValues;

    /**
     * Количество значений, участвующих в подсчёте релевантности профессии
     * Для чего:
     * 1. экономия ресурсов - меньше проходок - меньше расходов.
     */
    const NUMBER_VALUES_INVOLVED_IN_CALCULATION = 20;

    public function __construct(array $allValues, array $userValues)
    {
        self::preFilter($userValues);

        $this->allValues = $allValues;
        $this->userValues = $userValues;
    }

    /**
     * Расчитывет балл релевантности для системы ценностей.
     * Базовые принципы
     * Пользователь сортирует свои ценности.
     * Чем выше в списке (index 0) ценность, тем выше должна быть оценка профессии, если профессия имеют такую ценность
     * Вопрос - нужно ли сортировать ценности в самой профессии?
     *
     * @param ValueSystem $professionValueSystem - ценности профессии
     * @return float
     */
    public function calculate(ValueSystem $professionValueSystem): float
    {
        $professionValues = $professionValueSystem->values();
        self::preFilter($professionValues);

        $max = self::NUMBER_VALUES_INVOLVED_IN_CALCULATION;

        $sum = 0;
        foreach ($this->userValues as $index => $value) {
            if ($index >= $max) {
                break;
            }

            // todo добавить еще и учитывание порядок в профессии (попробовать)
            // формула - чем ближе к началу, тем больше оценка
            // Например, макс - 22, индекс = 0, значит оценка - 22
            // Например, макс - 22, индекс = 22, значит оценка - 0
            if (!in_array($value, $professionValues)) {
                $sum += 0;
            } else {
                // формула просто преобразует индекс в обраное число
                // получается сумма, где первая ценность всего на 1 больше, чем вторая ценность
                // это не годится.
//                $sum += $max - $index;

                // порабола с отрицательным и положительным значениями
                // эта функция хороша тем, что отдалённые значения
                // не просто имеют меньший эффект, а даже наоборот - уменьшают значения
                // 1 - -100
                // 2 - -81
                // 3 - -64
                // 4 - -49
                // 10 - -1
                // 20 - +81
                // 22 - +121
                $sum += pow($index + 1 - 11, 2);

                // 1 - 100
                // 2 - 50
                // 3 - 33
                // 4 - 25
                // 10 - 10
                // 20 - 5
                // 22 - 4.5
                // таким образом, эта формула хороша тем,
                // что даже самые отдалённые значения оказывают воздействие
//                $sum += 100 / ($index + 1);

                // чем точнее, тем меньше делитель, а значит больше число.
                // значение делимого не имеет значения, выбрано 100.
                // 1 - 100
                // 2 - 25
                // 3 - 11
                // 4 - 6.25
                // 10 - 1
                // 20 - 0.25
                // 22 - 0.2
                // таким образом, эта формула хороша тем, что после десятого значения
                // остальные значения почти не вносят вклада.
//                $sum += (100 / pow($index + 1, 2));
//                $sum += (1000 / pow(2 + 10, 2));

//                $sum += log(22 - $index, 2);
            }
        }

        return round($sum, 2);
//
//        // высчитывание релевантности (процент)
//        // to do судя по всему число ценностей, указанных в профессии напрямую влияет на результат.
//        //  получается, что если у Архитектора стоит одна ценность, а у Повара две, то Повар получит оценку выше.
//        //  поэтому нужно расчитывать релевантность, учитывая это.
//        //  например, сделать, чтобы число ценностей из профессии участвовало в подсчёте
//        $relevance = $sum * 100 / $max / count($this->allValues);
//
//        return $relevance;
    }

    /**
     * Расчитывет балл релевантности для системы ценностей.
     * Базовые принципы
     * Пользователь сортирует свои ценности.
     * Чем выше в списке (index 0) ценность, тем выше должна быть оценка профессии, если профессия имеют такую ценность
     * Вопрос - нужно ли сортировать ценности в самой профессии?
     *
     * @param ValueSystem $professionValueSystem - ценности профессии
     * @return float
     */
    public function calculatePercent(ValueSystem $professionValueSystem): float
    {
        $professionValues = $professionValueSystem->values();
        self::preFilter($professionValues);

        $countAllValues = count($this->allValues);

        $sum = 0;
        foreach ($this->userValues as $index => $value) {
            if (!in_array($value, $professionValues)) {
                $sum -= (22 - $index);
                continue;
            }

            // чем меньше индекс - тем выше оценка
            // 22 - 0 = 22
            $t1 = $countAllValues - $index;

//            $sum += $t1;
            //... проблема в том, что число ценностей в профессии разное...
            // чем больше ценностей в профессии, тем это.. что? лучше? хуже?
            // почему их только 5, а не 22?
            // может быть остальные тоже как бы есть, но их вес - 0?
            /**
             * Давайте рассуждать.
             * Художник - art,result,indoor,work-alone,intel,hands,light-work,self-employ,free-time,safe
             * Дизайнер - art,indoor,intel,light-work,free-time,safe
             * Филолог  - intel,indoor,safe,light-work,free-time
             * Эколог   - intel,safe,benefit,big-company
             * Гейм-диз - intel,people,art,result,safe,free-time,high-society,light-work,difference,indoor
             * Пилот    - travel,big-company,outdoor,high-society,salary,body,people,result,benefit
             * Математик- intel,work-alone,safe,big-company,result,indoor,free-time,light-work
             *
             * USER: 'art', 'big-company', 'safe', 'light-work', 'free-time', 'high-society', 'indoor', 'result', 'intel', 'benefit', 'difference', 'work-alone', 'people', 'publicity', 'hands', 'body', 'gov', 'travel', 'promotion', 'outdoor', 'self-employ', 'salary', 'prestige'
             *
             * Художник: 22 + 15 + 16 + 11 + 14 + 8 + 19 + 2 + 18 + 20 = 145
             * Дизайнер: 22 + 16 + 14 + 19 + 18 + 20 = 109
             * Филолог:  14 + 16 + 20 + 19 + 18 = 87
             * Эколог:   14 + 20 + 13 + 21 = 68 (68 / 4 = 17)
             * Гейм-диз: 13 + 10 + 22 + 15 + 20 + 18 + 17 + 19 + 12 + 16 = 162 (162 / 10 = 16.2)
             * Пилот:    5 + 21 + 3 + 16 + 2 + 7 + 10 + 15 + 12 = 91 (91 / 9 = 9.1)
             * Математик:14 + 11 + 20 + 21 + 15 + 16 + 18 + 19 = 134 (134 / 8 = 16.75)
             *
             * У них одни и те же ценности, но у художника их на 4 больше.
             *
             * Получается, чем меньше ценностей в профессии, тем меньше очки.
             * Это плохо?
             *
             * В данный момент порядок ценностей в профессии не имеет значения.
             * Это значит, что мы учитываем только факт их присутствия.
             * Это создаёт проблему.
             *
             * Как можно решить проблему меньшего числа ценностей?
             *
             * Решение 1:
             * Всегда указывать одинаковое число ценностей, например, 9.
             *
             * Решение 2:
             * Брать среднее. Но получится, что чем больше ценностей, тем больше риск несовпадения.
             * Филолог:  17.4
             * Эколог:   17
             * Гейм-диз: 16.5
             *
             * Решение 3:
             * Сделать параболу.
             */

            // вариант со средним
//            $sum += $t1 / count($professionValues);
            $sum += pow($t1 - 10, 2);

            // парабола
            // чем дальше значение
//            $sum += pow($index - 10, 2);
        }

        // 22 * 2 + 21 * 2

//        $maxSum = 462; // вычислил путём складывания 22*2 + 21*2 +20*2 ... = n

//        $score = $sum * 100 / $maxSum;

        return round($sum / count($professionValues), 2);

//        // высчитывание релевантности (процент)
//        // to do судя по всему число ценностей, указанных в профессии напрямую влияет на результат.
//        //  получается, что если у Архитектора стоит одна ценность, а у Повара две, то Повар получит оценку выше.
//        //  поэтому нужно расчитывать релевантность, учитывая это.
//        //  например, сделать, чтобы число ценностей из профессии участвовало в подсчёте
//        $relevance = $sum * 100 / $max / count($this->allValues);
//
//        return $relevance;
    }

    private function preFilter(array &$values): void
    {
        unset($values['prestige']);
        unset($values['salary']);
    }
}