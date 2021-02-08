<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test158Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->questionsHolder, $this->answersHolder);

        $values = [
            'fiz' => AnswersUtil::arraySum($sums, 'fiz') * 11,
            'verb' => AnswersUtil::arraySum($sums, 'verb') * 8,
            'kosv' => AnswersUtil::arraySum($sums, 'kosv') * 13,
            'podozr' => AnswersUtil::arraySum($sums, 'podozr') * 11,
            'obida' => AnswersUtil::arraySum($sums, 'obida') * 13,
            'vina' => AnswersUtil::arraySum($sums, 'vina') * 11,
            'negativ' => AnswersUtil::arraySum($sums, 'negativ') * 20,
            'razdraj' => AnswersUtil::arraySum($sums, 'razdraj') * 9,
            'ia' => AnswersUtil::arraySum($sums, 'ia') * 3,
            'iv' => AnswersUtil::arraySum($sums, 'iv') * 2,
        ];

        return array_merge($values, [
            'scale' => [
                'fiz' => round($values['fiz'] * 100 / 110),
                'verb' => round($values['verb'] * 100 / 104),
                'kosv' => round($values['kosv'] * 100 / 117),
                'negativ' => round($values['negativ'] * 100 / 100),
                'razdraj' => round($values['razdraj'] * 100 / 99),
                'podozr' => round($values['podozr'] * 100 / 99),
                'obida' => round($values['obida'] * 100 / 104),
                'vina' => round($values['vina'] * 100 / 99),
                'ia' => round($values['ia'] * 100 / 110),
                'iv' => round($values['iv'] * 100 / 101),
            ]
        ]);
    }
}