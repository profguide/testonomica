<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation\Calc;

use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculatorBasedOnParts;
use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionTypeScoreCalculatorBasedOnPartsTest extends KernelTestCase
{
    // один тип
    public function testThatOneHighestTypeProducesMaximumScore()
    {
        $userTypes = ['art' => 100];
        $professionTypes = new Types([new TypesCombination(['art' => 100])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        self::assertEquals(100, $calculator->calculate($professionTypes, $professionTypesNot));
    }

    public function testThatTwoHighestTypesProduceMaximumScore()
    {
        $userTypes = ['art' => 100, 'math' => 100];
        $professionTypes = new Types([new TypesCombination(['art' => 50, 'math' => 50])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        self::assertEquals(100, $calculator->calculate($professionTypes, $professionTypesNot));
    }

    public function testThatOneLowerTypeProducesLowerScore()
    {
        $userTypes = ['art' => 100, 'math' => 50];
        $professionTypes = new Types([new TypesCombination(['art' => 50, 'math' => 50])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        self::assertEquals(75, round($calculator->calculate($professionTypes, $professionTypesNot)));
    }

    public function testThatTwoLowerTypeProduceLowerScore()
    {
        $userTypes = ['art' => 50, 'math' => 50];
        $professionTypes = new Types([new TypesCombination(['art' => 50, 'math' => 50])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        self::assertEquals(50, round($calculator->calculate($professionTypes, $professionTypesNot)));
    }

    public function testThatVeryLowTypeProduceMinimumScore()
    {
        $userTypes = ['art' => 0, 'math' => 0];
        $professionTypes = new Types([new TypesCombination(['art' => 50, 'math' => 50])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        self::assertEquals(0, round($calculator->calculate($professionTypes, $professionTypesNot)));
    }

    public function testThatHigherTypeTakesOverLowExpectations()
    {
        $userTypes = ['art' => 100, 'math' => 100];
        $professionTypes = new Types([new TypesCombination(['art' => 100, 'math' => 0])]); // << 0 doest matter
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        self::assertEquals(100, round($calculator->calculate($professionTypes, $professionTypesNot)));
    }

    // не уверен, но возможно более низкие ожидания должны приводить так же к более низкому очку, хотя... может наоборот?
    public function test2()
    {
        $userTypes = ['art' => 100, 'math' => 100];
        $professionTypes = new Types([new TypesCombination(['art' => 80, 'math' => 20])]);
        $professionTypesNot = new TypesCombination([]);

        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        self::assertEquals(100, round($calculator->calculate($professionTypes, $professionTypesNot)));
    }
}