<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\Proforientation\Sex;
use App\Tests\Test\Proforientation\Calc\ProfessionSexScoreCalculatorTest;

/**
 * @see ProfessionSexScoreCalculatorTest
 */
final readonly class ProfessionSexScoreCalculator
{
    public function __construct(private Sex $userSex)
    {
    }

    public function calculate(Sex $sex): Score
    {
        $score = 100;

        if ($this->userSex != Sex::NONE && $sex != Sex::NONE) {
            if ($this->userSex != $sex) {
                $score = 0;
            }
        }

        return new Score($score);
    }
}