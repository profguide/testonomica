<?php
/**
 * @author: adavydov
 * @since: 22.12.2020
 */

namespace App\Test;


use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractComplexCalculator implements CalculatorInterface
{
    /**@var CalculatorInterface[] */
    protected $calculators;

    /**@var KernelInterface */
    protected $kernel;

    public function __construct(array $calculators, KernelInterface $kernel)
    {
        $this->calculators = $calculators;
        $this->kernel = $kernel;
    }

    public function calculate(): array
    {
        $result = [];
        foreach ($this->calculators as $id => $calculator) {
            $result[$id] = $calculator->calculate();
        }
        return $result;
    }
}