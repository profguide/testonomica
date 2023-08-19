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
        $sums = AnswersUtil::sumByGroups($this->questionsHolder, $this->answersHolder);

        $scales = [];
        foreach ($sums as $name => $sum) {
            $max = AnswersUtil::maxInGroup($this->questionsHolder, $name);
            $scales[$name] = [
                'sum' => $sum,
                'max' => $max,
                'percentage' => round($sum * 100 / $max),
            ];
        }

        $aggressionMax = $scales['fiz']['max'] + $scales['razdraj']['max'] + $scales['vina']['max'];
        $aggressionSum = $scales['fiz']['sum'] + $scales['razdraj']['sum'] + $scales['vina']['sum'];
        $scales['index_aggression'] = [
            'sum' => $aggressionSum,
            'max' => $aggressionMax,
            'percentage' => round($aggressionSum * 100 / $aggressionMax),
        ];

        $hostileMax = $scales['obida']['max'] + $scales['podozr']['max'];
        $hostileSum = $scales['obida']['sum'] + $scales['podozr']['sum'];
        $scales['index_hostile'] = [
            'sum' => $hostileSum,
            'max' => $hostileMax,
            'percentage' => round($hostileSum * 100 / $hostileMax),
        ];

        return $scales;
    }
}