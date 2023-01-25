<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;

/**
 * @see ProfessionTypeScoreCalculatorTest
 */
final class ProfessionTypeScoreCalculator
{
    private array $userTypes;

    public function __construct(array $userTypes)
    {
        $this->userTypes = $userTypes;
    }

    public function calculate(Types $types, TypesCombination $not): float
    {
        $max = 0;
        foreach ($types->combinations() as $comb) {
            $rating = $this->scoreCombination($comb, $not);
            if ($rating > $max) {
                $max = $rating;
            }
        }

        return (float)$max;
    }

    /**
     * @param TypesCombination $types
     * @param TypesCombination $not - не учитывать комбинации, где присутствуют опредённые типы.
     * Пригождается, чтобы отсечь профессии, не требовательные к сложным навыками, когда человек их набрал.
     * Например, слесарь - только body, а человек набрал и body и human и it. Если в профессии указано not="human",
     * то рейтинг будет 0
     * @return float
     */
    private function scoreCombination(TypesCombination $types, TypesCombination $not): float
    {
        $keysTypesScored = array_keys($this->userTypes);

        // если не набраны все требуемые типы, то это не подходит
        foreach ($types->values() as $typeNeed) {
            if (!in_array($typeNeed, $keysTypesScored)) {
                return 0;
            }
        }

        // если набранный тип указан в $not, профессия не подходит
        foreach (array_keys($this->userTypes) as $typeScored) {
            if (in_array($typeScored, $not->values())) {
                return 0;
            }
        }

        // сложим значения набранных типов
        $sum = 0;
        foreach ($this->userTypes as $type => $value) {
            if (in_array($type, $types->values())) {
                $sum += $value;
            }
        }

        // среднее арифметическое
        return $sum; // / count($types->values());
    }
}