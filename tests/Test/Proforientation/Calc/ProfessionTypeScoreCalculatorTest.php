<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation\Calc;

use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculator;
use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionTypeScoreCalculatorTest extends KernelTestCase
{
    public function testIfCombinationNotMatchThenScoreZero()
    {
        $userTypes = ['art' => 60, 'it' => 60];
        $professionTypes = new Types([new TypesCombination(['art', 'math'])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculator($userTypes);
        self::assertEquals(0, $calculator->calculate($professionTypes, $professionTypesNot));
    }

    public function testCombinationMatch()
    {
        $userTypes = ['art' => 60, 'math' => 60, 'it' => 10];
        $professionTypes = new Types([new TypesCombination(['art', 'math'])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculator($userTypes);
        self::assertEquals(120, $calculator->calculate($professionTypes, $professionTypesNot));
    }

    public function testCombinationMatchGetBestScore()
    {
        $userTypes = ['art' => 60, 'math' => 60, 'it' => 10, 'boss' => 50];
        $professionTypes = new Types([
            new TypesCombination(['art', 'math']),
            new TypesCombination(['it', 'boss'])
        ]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculator($userTypes);
        self::assertEquals(120, $calculator->calculate($professionTypes, $professionTypesNot));
    }
}