<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation\Calc;

use App\Test\Proforientation\Calc\TopTypesCalculator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class TopTypesCalculatorTest extends KernelTestCase
{
    public function testAllEqualSliceCorrect()
    {
        $allEqual = ['natural' => 100, 'tech' => 100, 'human' => 100, 'body' => 100, 'math' => 100, 'it' => 100, 'craft' => 100, 'art' => 100, 'hoz' => 100, 'com' => 100, 'boss' => 100, 'war' => 100];

        $result = (new TopTypesCalculator())->calc($allEqual);
        self::assertCount(4, $result);
    }

    public function testSimpleOrder()
    {
        $source = ['natural' => 120, 'tech' => 110, 'human' => 100, 'body' => 90, 'math' => 80, 'it' => 70, 'craft' => 60, 'art' => 50, 'hoz' => 40, 'com' => 30, 'boss' => 20, 'war' => 10];

        $result = (new TopTypesCalculator())->calc($source);
        self::assertEquals(['natural' => 120, 'tech' => 110, 'human' => 100, 'body' => 90], $result);
    }

    public function testComplex()
    {
        $source = ['natural' => 90, 'tech' => 50, 'human' => 45, 'body' => 30, 'math' => 25, 'it' => 25, 'craft' => 25, 'art' => 25, 'hoz' => 25, 'com' => 25, 'boss' => 25, 'war' => 25];

        $result = (new TopTypesCalculator())->calc($source);
        // 50 не проходит потому что слишком маленькое значение (55% от 90)
        // остальные еще меньше
        self::assertEquals(['natural' => 90], $result);
    }
}