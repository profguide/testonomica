<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation;

use App\Test\Proforientation\Profession;
use App\Test\Proforientation\Calc\ProfessionsPercentCalculator;
use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;
use App\Test\Proforientation\ValueSystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionsPercentCalculatorTest extends KernelTestCase
{
    public function testCalculator()
    {
        $one = new Profession('Маркетолог', new Types([new TypesCombination(['art' => 100, 'math' => 100])]), new TypesCombination([]), new ValueSystem([]), []);
        $one->setRating(50);
        $two = new Profession('Архитектор', new Types([new TypesCombination(['art' => 100, 'math' => 100])]), new TypesCombination([]), new ValueSystem([]), []);
        $two->setRating(80);

        $professions = [
            $one, $two
        ];

        $calculator = new ProfessionsPercentCalculator();
        $calculator->calculate($professions);

        // убедимся, что порядок не поменялся - калькулятор не меняет его
        self::assertEquals('Маркетолог', $professions[0]->name());

        self::assertEquals(62.5, $professions[0]->getRating());
        self::assertEquals(100, $professions[1]->getRating());
    }
}