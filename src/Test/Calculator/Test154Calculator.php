<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test154Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->questionsHolder, $this->answersHolder);
        return array_merge($sums, [
            'scale' => [
                'vremennoe' => round(AnswersUtil::arraySum($sums, 'vremennoe') * 100 / 10),
                'emocionalnoe' => round(AnswersUtil::arraySum($sums, 'emocionalnoe') * 100 / 10),
                'postoyannoe' => round(AnswersUtil::arraySum($sums, 'postoyannoe') * 100 / 10),
                'povedencheskoe' => round(AnswersUtil::arraySum($sums, 'povedencheskoe') * 100 / 10),
                'kognitivnoe' => round(AnswersUtil::arraySum($sums, 'kognitivnoe') * 100 / 10),
            ]
        ]);
    }
}