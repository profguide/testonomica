<?php

declare(strict_types=1);

namespace App\Test\Helper;

use App\Test\Proforientation\Calc\Score;
use App\Test\Proforientation\ValueSystem;

final class ProfessionValueSystemRelevanceCalculator
{
    private array $allValues;

    private array $userValues;

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
     * @return Score
     */
    public function calculatePercent(ValueSystem $professionValueSystem): Score
    {
        $professionValues = $professionValueSystem->values();
        self::preFilter($professionValues);

        $countAllValues = count($this->allValues);

        $sum = 0;
        foreach ($this->userValues as $index => $name) {
            if (!in_array($name, $professionValues)) {
                continue;
            }

            // чем меньше индекс - тем выше оценка
            // 22 - 0 = 22
            $t1 = $countAllValues - $index;

            $sum += pow($t1, 2);
        }

        // среднее значение - чтоб количество не влияло
        $score = round($sum / count($professionValues), 2);

        return new Score($score, ['sum' => $sum]);
    }

    private function preFilter(array &$values): void
    {
        unset($values['prestige']);
        unset($values['salary']);
    }
}