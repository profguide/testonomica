<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Tests\Test\Calculator;

use App\Test\Calculator\ProforientationTeenCalculator;

class ProforientationTeenCalculatorTest extends AbstractProforientationCalculatorTest
{
    public function setUp()
    {
        self::bootKernel();
        $this->calculator = new ProforientationTeenCalculator(self::$kernel);
    }

    protected function getSrcFilename(): string
    {
        return self::$kernel->getProjectDir() . "/xml/proforientationTeen.xml";
    }
}