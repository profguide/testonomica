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
        $this->userValues = $this->prepareSystemValues($userValues);
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
     * Дополняет список ценностей отсутствующими значенями
     * Внимание! это влияет на порядок
     * Метод был добавлен для тестирования, чтобы не мучиться с указанием всех 22 ценностей
     * @param array $values
     * @return array
     */
    private function prepareSystemValues(array $values): array
    {
        foreach ($this->allValues as $value) {
            if (!in_array($value, $values)) {
                $values[] = $value;
            }
        }
        return $values;
    }

    private function preFilter(array &$values): void
    {
        unset($values['prestige']);
        unset($values['salary']);
    }
}