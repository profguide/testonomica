<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation\Calc;

use App\Test\Proforientation\Calc\ComplexTypeAwardCalculator;
use App\Test\Proforientation\TypesCombination;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ComplexTypeAwardCalculatorTest extends KernelTestCase
{
    public function testComplexOne()
    {
        $comb = new TypesCombination(['art' => 100]);

        self::assertEquals(0, ComplexTypeAwardCalculator::calculate($comb));
    }

    public function testComplexTwo()
    {
        $comb = new TypesCombination(['art' => 100, 'math' => 100]);

        self::assertEquals(15, ComplexTypeAwardCalculator::calculate($comb));
    }

    public function testComplexThree()
    {
        $comb = new TypesCombination(['art' => 100, 'math' => 100, 'body' => 100]);

        self::assertEquals(45, ComplexTypeAwardCalculator::calculate($comb));
    }

    public function testComplexFour()
    {
        $comb = new TypesCombination(['art' => 100, 'math' => 100, 'body' => 100, 'tech' => 100]);

        self::assertEquals(90, ComplexTypeAwardCalculator::calculate($comb));
    }

    public function testComplexFive()
    {
        $comb = new TypesCombination(['art' => 100, 'math' => 100, 'body' => 100, 'tech' => 100, 'war' => 100]);

        self::assertEquals(150, ComplexTypeAwardCalculator::calculate($comb));
    }

    public function testComplexSix()
    {
        $comb = new TypesCombination(['art' => 100, 'math' => 100, 'body' => 100, 'tech' => 100, 'war' => 100, 'human' => 100]);

        self::assertEquals(225, ComplexTypeAwardCalculator::calculate($comb));
    }
}