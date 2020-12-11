<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Tests\Test\Calculator;

use App\Test\Calculator\TeenProforientationCalculator;

class ProforientationTeenCalculatorTest extends AbstractProforientationCalculatorTest
{
    public function setUp()
    {
        self::bootKernel();
        $this->calculator = new TeenProforientationCalculator(self::$kernel);
    }

    protected function getSrcFilename(): string
    {
        return self::$kernel->getProjectDir() . "/xml/proforientationTeen.xml";
    }
}