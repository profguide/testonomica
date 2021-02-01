<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test121Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->answersHolder);
        $one = AnswersUtil::arraySum($sums, 'odin') * 2;
        $two = AnswersUtil::arraySum($sums, 'dva') * 3;
        $three = AnswersUtil::arraySum($sums, 'tri') * 4;
        return [
            'sum' => $sum = $one + $two + $three,
            'scale' => round($sum * 100 / 132)
        ];
    }
}