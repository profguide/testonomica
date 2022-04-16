<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Tests\Test\Calculator;

class ProforientationTeenCalculatorTest extends AbstractProforientationCalculatorTest
{
    protected $calculatorName = 'App\Test\Calculator\ProforientationTeenCalculator';

    public function setUp(): void
    {
        self::bootKernel();
    }

    protected function getSrcFilename(): string
    {
        return self::$kernel->getProjectDir() . "/xml/proftest.xml";
    }
}