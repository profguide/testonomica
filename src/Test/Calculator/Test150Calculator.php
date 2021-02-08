<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test150Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        return [
            'sum' => $sum = AnswersUtil::sum($this->questionsHolder, $this->answersHolder),
            'scale' => round($sum * 100 / 130),
        ];
    }
}