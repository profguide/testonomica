<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test119Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->questionsHolder, $this->answersHolder);
        $chasto = AnswersUtil::arraySum($sums, 'chasto') * 3;
        $inogda = AnswersUtil::arraySum($sums, 'inogda') * 2;
        $redko = AnswersUtil::arraySum($sums, 'redko');

        return [
            'sum' => $sum = $chasto + $inogda + $redko,
            'scale' => round($sum * 100 / 60),
        ];
    }
}