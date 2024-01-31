<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation\Calc;

use App\Test\Proforientation\Calc\ProfessionSexScoreCalculator;
use App\Test\Proforientation\Sex;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionSexScoreCalculatorTest extends KernelTestCase
{
    public function testSuitable100Percent(): void
    {
        $calculator = new ProfessionSexScoreCalculator(Sex::MALE);
        $score = $calculator->calculate(Sex::MALE)->value();
        self::assertEquals(100, $score);
    }

    public function testIndifferent100Percent()
    {
        $calculator = new ProfessionSexScoreCalculator(Sex::MALE);
        $score = $calculator->calculate(Sex::NONE)->value();
        self::assertEquals(100, $score);
    }

    public function testIndifferent100Percent2()
    {
        $calculator = new ProfessionSexScoreCalculator(Sex::NONE);
        $score = $calculator->calculate(Sex::MALE)->value();
        self::assertEquals(100, $score);
    }

    public function testUnsuitable0Percent()
    {
        $calculator = new ProfessionSexScoreCalculator(Sex::MALE);
        $score = $calculator->calculate(Sex::FEMALE)->value();
        self::assertEquals(0, $score);
    }
}