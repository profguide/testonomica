<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test120Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->answersHolder);
        return [
            'sum' => $sum = AnswersUtil::arraySum($sums, 0),
            'scale' => round($sum * 100 / 16)
        ];
    }
}