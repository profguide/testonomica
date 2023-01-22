<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

/**
 * @see TopTypesCalculatorTest
 */
final class TopTypesCalculator
{
    // а почему 4? философский вопрос, топ - это сколько?
    private const NUMBER = 4;

    /**
     * Фильтрует топовые типы
     * @param array $values ['tech' => 20, 'body' => 50, 'human' => 0, 'craft' => 40]
     * @return array ['body' => 50, 'craft' => 40]
     */
    public function calc(array $values): array
    {
        // $top = self::medianBased($values);
//        $top = self::fiftyPercentBased($values);
        $top = self::seventyFivePercentBased($values);

        return array_slice($top, 0, self::NUMBER);
    }

    /**
     * @param array $values
     * @return array
     */
    private static function seventyFivePercentBased(array $values): array
    {
        // максимальное значение первое
        $max = reset($values);

        // минимальное значение
        $min = $max / 1.5;

        return array_filter($values, function (float $value) use ($min) {
            return $value >=  $min;
        });
    }

    /**
     * Расчёт на основе формулы = fn(max/2) - всё что выше половины максимума - топ.
     * @param array $values
     * @return array
     */
    private static function fiftyPercentBased(array $values): array
    {
        // максимальное значение первое
        $max = reset($values);

        // минимальное значение - 50% от максимума
        $min = $max / 2;

        return array_filter($values, function (float $value) use ($min) {
            return $value >= $min;
        });
    }

    /**
     * Расчёт на основе медианного значения
     * Логично, но оказалось, что в ряду 90, 50, 45, 30, 25..... пропускает 30 (а это 33% от 90)
     * @param array $values
     * @return array
     */
    private static function medianBased(array $values): array
    {
        // отсортируем
        arsort($values);

        // отрежем половину
        $half = array_slice($values, 0, 6);

        // найдём медианное значение этой половины
        $median = $half[array_key_first(array_slice($half, 3, 1))];

        // топ - всё чо выше медианного значения
        return array_filter($half, function (float $value) use ($median) {
            return $value >= $median;
        });
    }
}