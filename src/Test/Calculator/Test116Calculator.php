<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test116Calculator extends Test114Calculator
{
    protected int $max = 50;

    public function calculate(): array
    {
        return parent::calculate();
    }
}