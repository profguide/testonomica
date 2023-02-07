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
        $professionTypes = new Types([new TypesCombination(['art' => 50, 'math' => 50])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnTopTypes($userTypes);
        self::assertEquals(0, $calculator->calculate($professionTypes, $professionTypesNot)->value());
    }

    public function testCombinationMatch()
    {
        $userTypes = ['art' => 100, 'math' => 100, 'it' => 100];
        $professionTypes = new Types([new TypesCombination(['art' => 50, 'math' => 50])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnTopTypes($userTypes);
        // (100 + 100 + complexAward) / 2 = 107.5
        self::assertEquals(107.5, $calculator->calculate($professionTypes, $professionTypesNot)->value());
    }

    public function testCombinationMatchGetBestScore()
    {
        $userTypes = ['art' => 100, 'math' => 100, 'boss' => 100, 'it' => 50];
        $professionTypes = new Types([
            new TypesCombination(['art' => 50, 'math' => 50]), // << the best
            new TypesCombination(['boss' => 50, 'it' => 50])
        ]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnTopTypes($userTypes);
        // (100 + 100 + complexAward) / 2 = 107.5
        // косвенно узнали, что выбрана комбинация, где все по 100.
        self::assertEquals(107.5, $calculator->calculate($professionTypes, $professionTypesNot)->value());
    }
}