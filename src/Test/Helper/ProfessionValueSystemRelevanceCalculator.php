<?php

declare(strict_types=1);

namespace App\Test\Helper;

use App\Test\Proforientation\ValueSystem;
use InvalidArgumentException;

final class ProfessionValueSystemRelevanceCalculator
{
    private array $allValues;

    private array $userValues;

    /**
     * Количество значений, участвующих в подсчёте релевантности профессии
     * Для чего:
     * 1. экономия ресурсов - меньше проходок - меньше расходов.
     */
    const NUMBER_VALUES_INVOLVED_IN_CALCULATION = 10;

    public function __construct(array $allValues, array $userValues)
    {
        self::guardUserValues($userValues);
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

            // todo добавить все-таки еще и учитывание порядок в профессии (попробовать)

            // формула - чем ближе к началу, тем больше оценка
            // Например, макс - 22, индекс = 0, значит оценка - 22
            // Например, макс - 22, индекс = 22, значит оценка - 0
            if (!in_array($value, $professionValues)) {
                $valueEvaluation = 0;
            } else {
                $valueEvaluation = $max - $index;
            }

            $sum += $valueEvaluation;
        }

        return $sum;
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

    private static function guardUserValues(array $userValues)
    {
        if (count($userValues) < self::NUMBER_VALUES_INVOLVED_IN_CALCULATION) {
            throw new InvalidArgumentException('user values number must be at least ' . self::NUMBER_VALUES_INVOLVED_IN_CALCULATION);
        }
    }
}