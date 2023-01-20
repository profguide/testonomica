<?php

declare(strict_types=1);

namespace App\Tests\Test\Proforientation;

use App\Test\Helper\ProfessionsMapper;
use App\Test\Proforientation\Profession;
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
            self::assertNotEmpty($profession->getCombs(), "Assert that {$profession->getName()}`s types are not empty.");
        }
    }

    public function testTypesValuesCorrect()
    {
        // 'natural', 'tech', 'human', 'body', 'math', 'it', 'craft', 'art', 'hoz', 'com', 'boss', 'war'

        // todo типы вытащить в value-object, чтобы легче было работать с ним:
        //  $profession->types()->values()
        //  $profession->not()->values()

        // 1. (ГОТОВО) Types переименовать в ProftestConfigTypes
        // 2. (ГОТОВО) Создать Types (value-object)
        // 3. написать хелпер (калькулятор по типу ProfessionValueSystemRelevanceCalculator)
        //  - ProfessionTypeRelevanceCalculator (это который Types в данный момент)

//        foreach ($this->professions as $profession) {
//            foreach ($profession->valueSystem()->values() as $value) {
//                self::assertContains($value, ValueSystem::ALL, "Assert that {$profession->getName()}`s types are correct.");
//            }
//        }
    }

    // Value System

    public function testEveryProfessionHasValueSystem()
    {
        foreach ($this->professions as $profession) {
            self::assertNotEmpty($profession->valueSystem()->values(), "Assert that {$profession->getName()}`s value system are not empty.");
        }
    }

    public function testValueSystemLimit()
    {
        $limit = self::SYSTEM_VALUES_ALLOWED_LIMIT;

        foreach ($this->professions as $profession) {
            $count = count($profession->valueSystem()->values());

            self::assertTrue($count <= $limit, "Assert that {$profession->getName()}`s value system are within the limit.");
        }
    }

    public function testValueSystemValuesCorrect()
    {
        foreach ($this->professions as $profession) {
            foreach ($profession->valueSystem()->values() as $value) {
                self::assertContains($value, ValueSystem::ALL, "Assert that {$profession->getName()}`s value system are correct.");
            }
        }
    }
}