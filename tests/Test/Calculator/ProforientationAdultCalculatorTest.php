<?php
/**
 * @author: adavydov
 * @since: 09.12.2020
 */

namespace App\Tests\Test\Calculator;

use App\Test\Calculator\ProforientationAdultCalculator;

class ProforientationAdultCalculatorTest extends AbstractProforientationCalculatorTest
{
    public function setUp()
    {
        self::bootKernel();
        $this->calculator = new ProforientationAdultCalculator(self::$kernel);
    }

    protected function getSrcFilename(): string
    {
        return self::$kernel->getProjectDir() . "/xml/proforientationAdult.xml";
    }
}