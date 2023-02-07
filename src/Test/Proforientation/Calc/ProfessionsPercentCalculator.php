<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\Proforientation\Profession;

/**
 * Переводит счёт профессий в проценты. Процент высчитывается из макисмального счёта,
 * который выясняется тут же.
 *
 * @see ProfessionsPercentCalculatorTest
 */
final class ProfessionsPercentCalculator
{
    /**
     * @param Profession[] $professions
     */
    public function calculate(array &$professions)
    {
        self::setAll($professions, self::calcMaxScore($professions));
    }

    /**
     * @param Profession[] $professions
     * @param float $max
     */
    private static function setAll(array &$professions, float $max)
    {
        foreach ($professions as $profession) {
            if ($profession->getRating() > 0) {
                $percent = $profession->getRating() * 100 / $max;
                $profession->setRating(round($percent, 2));
            }
        }
    }

    /**
     * @param Profession[] $professions
     * @return float
     */
    private static function calcMaxScore(array $professions): float
    {
        $max = 0;
        foreach ($professions as $profession) {
            $rating = $profession->getRating();
            if ($rating > $max) {
                $max = $rating;
            }
        }
        return $max;
    }
}