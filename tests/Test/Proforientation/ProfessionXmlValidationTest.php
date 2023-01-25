<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation;

use App\Test\Helper\ProfessionsMapper;
use App\Test\Proforientation\Profession;
use App\Test\Proforientation\TypesCombination;
use App\Test\Proforientation\ValueSystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionXmlValidationTest extends KernelTestCase
{
    /**@var Profession[] $professions */
    private array $professions = [];

    const SYSTEM_VALUES_ALLOWED_LIMIT = 10;

    public function setUp(): void
    {
        self::bootKernel();

        $xml = self::$kernel->getProjectDir() . '/xml/proftest/professions.xml';
        $mapper = new ProfessionsMapper(file_get_contents($xml), 'ru');
        $this->professions = $mapper->getProfessions();
    }

    // Types

    public function testEveryProfessionHasTypes()
    {
        foreach ($this->professions as $profession) {
            self::assertNotEmpty($profession->types(), "Assert that types not empty at {$profession->name()}.");
        }
    }

    public function testTypesValuesCorrect()
    {
        foreach ($this->professions as $profession) {
            foreach ($profession->types()->combinations() as $i => $comb) {
                $sum = 0;
                foreach ($comb->values() as $name => $value) {
                    $sum += $value;
                    self::assertContains($name, TypesCombination::ALL, "Assert that type name is correct at {$profession->name()}.");
                }
                self::assertEquals(100, $sum, "Assert that {$profession->name()}`s types sum equals 100 at combination #$i.");
            }
        }
    }

    public function testTypesNotValuesCorrect()
    {
        foreach ($this->professions as $profession) {
            foreach ($profession->typesNot()->values() as $value) {
                self::assertContains($value, TypesCombination::ALL, "Assert that types are correct at {$profession->name()}.");
            }
        }
    }

    // Value System

    public function testEveryProfessionHasValueSystem()
    {
        foreach ($this->professions as $profession) {
            self::assertNotEmpty($profession->valueSystem()->values(), "Assert that value system is not empty at {$profession->name()}.");
        }
    }

    public function testValueSystemLimit()
    {
        $limit = self::SYSTEM_VALUES_ALLOWED_LIMIT;

        foreach ($this->professions as $profession) {
            $count = count($profession->valueSystem()->values());

            self::assertTrue($count <= $limit, "Assert that value system is within the limit at {$profession->name()}.");
        }
    }

    public function testValueSystemValuesCorrect()
    {
        foreach ($this->professions as $profession) {
            foreach ($profession->valueSystem()->values() as $value) {
                self::assertContains($value, ValueSystem::ALL, "Assert that value system is correct {$profession->name()}.");
            }
        }
    }
}