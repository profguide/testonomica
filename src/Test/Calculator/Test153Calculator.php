<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test153Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sum = round(AnswersUtil::sum($this->questionsHolder, $this->answersHolder) / 20, 2);
        return [
            'sum' => $sum,
            'scale' => round($sum * 100 / 4),
        ];
    }
}