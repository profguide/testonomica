<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;

class TestCalculator extends AbstractCalculator
{
    function calculate(): array
    {
        return $this->answersHolder->getAll();
    }
}