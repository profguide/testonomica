<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation;

use App\Test\Proforientation\Profession;
use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;
use App\Test\Proforientation\ValueSystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionTest extends KernelTestCase
{
    public function testConstructor()
    {
        $name = 'Архитектор';
        $types = new Types([new TypesCombination(['art', 'com'])]);
        $typesNot = new TypesCombination([]);
        $valueSystem = new ValueSystem([]);
        $description = [];

        self::assertNotNull(new Profession($name, $types, $typesNot, $valueSystem, $description));
    }
}