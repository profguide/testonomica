<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test117Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->answersHolder);
        return [
            'sum' => $sum = AnswersUtil::arraySum($sums, 'da', 'net'),
            'percentage' => round($sum * 100 / 50),
            'true_sum' => $trueSum = AnswersUtil::arraySum($sums, 'true'),
            'true_percentage' => round($trueSum * 100 / 50),
        ];
    }
}