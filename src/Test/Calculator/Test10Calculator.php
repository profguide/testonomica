<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test10Calculator extends AbstractCalculator
{
    function calculate(): array
    {
        return AnswersUtil::percentageWithValues($this->answersHolder, 12);
    }
}