<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test122Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->answersHolder);
        $one = AnswersUtil::arraySum($sums, 'odin');
        $two = AnswersUtil::arraySum($sums, 'dva') * 2;
        $three = AnswersUtil::arraySum($sums, 'tri') * 3;
        return [
            'sum' => $sum = $one + $two + $three,
            'scale' => round($sum * 100 / 60),
            'odin' => $one,
            'tri' => $three
        ];
    }
}