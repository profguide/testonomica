<?php
/**
 * @author: adavydov
 * @since: 09.12.2020
 */

namespace App\Tests\Test\Calculator;

class ProforientationAdultCalculatorTest extends AbstractProforientationCalculatorTest
{
    protected $calculatorName = 'App\Test\Calculator\ProforientationAdultCalculator';

    public function setUp()
    {
        self::bootKernel();
    }

    protected function getSrcFilename(): string
    {
        return self::$kernel->getProjectDir() . "/xml/proforientationAdult.xml";
    }
}