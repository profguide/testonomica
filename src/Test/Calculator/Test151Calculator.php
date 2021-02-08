<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;

class Test151Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumByGroups($this->questionsHolder, $this->answersHolder);
        return [
            'sum' => $sum = $sums['tich'] + $sums['toch'] + $sums['vom'],
            'tich' => $sums['tich'],
            'toch' => $sums['toch'],
            'vom' => $sums['vom'],
            'scale' => [
                'sum' => round($sum * 100 / 100),
                'tich' => round($sums['tich'] * 100 / 35),
                'toch' => round($sums['toch'] * 100 / 25),
                'vom' => round($sums['vom'] * 100 / 40),
            ]
        ];
    }
}