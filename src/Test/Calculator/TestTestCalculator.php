<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;

/**
 * This is a calculator for Unit tests
 * Class TestCalculator
 * @package App\Test\Calculator
 */
class TestTestCalculator extends AbstractCalculator
{
    function calculate(): array
    {
        return $this->answersHolder->getAll();
    }
}