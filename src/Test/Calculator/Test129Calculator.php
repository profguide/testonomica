<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test129Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->answersHolder);
        return [
            'sum' => $sum = AnswersUtil::arraySum($sums, 'key'),
            'scale' => round($sum * 100 / 23)
        ];
    }
}