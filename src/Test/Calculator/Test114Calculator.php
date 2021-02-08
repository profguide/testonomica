<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test114Calculator extends AbstractCalculator
{
    protected int $max = 32;

    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->questionsHolder, $this->answersHolder);
        unset($sums['0']);
        return [
            'sum' => $sum = array_sum($sums),
            'percentage' => round($sum * 100 / $this->max),
        ];
    }
}