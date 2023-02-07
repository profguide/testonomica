<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\Proforientation\TypesCombination;

/**
 * Высчитывает набавку за сложность типа
 * @see
 */
final class ComplexTypeAwardCalculator
{
    public static function calculate(TypesCombination $types): int
    {
        $typesCount = count($types->values());
        if ($typesCount < 2) {
            return 0;
        }

//        return count($types->values()) * 10; // 10 / 20 / 30 / 40 / 50

        $sum = 0;
        for ($i = 0; $i < $typesCount; $i++) {
            // 5: 0, 5, 15, 30, 50, 80
            // 15: 0, 15, 45, 90, 150
            // есть еще идея: добавлять не общий счёт, а каждому типу в отдельности - только добор, а не перебор.
            $sum += $i * 15;
        }

        return (int)$sum;
    }
}