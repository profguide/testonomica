<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation;

use App\Test\Proforientation\Profession;
use App\Test\Proforientation\ValueSystem;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionTest extends KernelTestCase
{
    public function testCombCorrect()
    {
        self::assertNotNull(new Profession('Архитектор', [['art', 'com']], new ValueSystem([]), [], []));
    }

    public function testEmptyCombCorrect()
    {
        self::assertNotNull(new Profession('Архитектор', [], new ValueSystem([]), [], []));
    }

    public function testCombWrongArrayThrowException()
    {
        $this->expectExceptionObject(new InvalidArgumentException('Every combination must be an array.'));
        new Profession('Архитектор', ['art', 'com'], new ValueSystem([]), [], []);
    }
}