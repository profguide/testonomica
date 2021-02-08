<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;

class Test149Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumByGroups($this->questionsHolder, $this->answersHolder);
        return [
            't' => $sums['t'],
            'f' => $sums['f'],
            'a' => $sums['a'],
            'r' => $sums['r'],
            'scale' => [
                't' => round($sums['t'] * 100 / 20),
                'f' => round($sums['f'] * 100 / 20),
                'a' => round($sums['a'] * 100 / 20),
                'r' => round($sums['r'] * 100 / 20),
            ],
        ];
    }
}