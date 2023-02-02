<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation\Calc;

use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculatorBasedOnTopTypes;
use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionTypeScoreCalculatorBasedOnTopTypesTest extends KernelTestCase
{
    public function testIfCombinationNotMatchThenScoreZero()
    {
        $userTypes = ['art' => 60, 'it' => 60];
        $professionTypes = new Types([new TypesCombination(['art', 'math'])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnTopTypes($userTypes);
        self::assertEquals(0, $calculator->calculate($professionTypes, $professionTypesNot)->value());
    }

    public function testCombinationMatch()
    {
        $userTypes = ['art' => 60, 'math' => 60, 'it' => 10];
        $professionTypes = new Types([new TypesCombination(['art' => 50, 'math' => 50])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnTopTypes($userTypes);
        self::assertEquals(120, $calculator->calculate($professionTypes, $professionTypesNot)->value());
    }

    public function testCombinationMatchGetBestScore()
    {
        $userTypes = ['art' => 60, 'math' => 60, 'it' => 10, 'boss' => 50];
        $professionTypes = new Types([
            new TypesCombination(['art' => 50, 'math' => 50]),
            new TypesCombination(['it' => 50, 'boss' => 50])
        ]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnTopTypes($userTypes);
        self::assertEquals(120, $calculator->calculate($professionTypes, $professionTypesNot)->value());
    }
}