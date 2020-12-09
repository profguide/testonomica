<?php
/**
 * @author: adavydov
 * @since: 09.12.2020
 */

namespace App\Tests\Test\Calculator;


use App\Test\Calculator\ProforientationAdultCalculator;
use Symfony\Component\HttpKernel\KernelInterface;

class ProforientationAdultCalculatorTest extends Proforientation2CalculatorTest
{
    public function setUp()
    {
        self::bootKernel();
        /**@var KernelInterface $appKernel */
        $appKernel = self::$container->get(KernelInterface::class);
        $this->calculator = new ProforientationAdultCalculator($appKernel);
    }
}