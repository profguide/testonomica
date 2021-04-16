<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test109Calculator extends AbstractCalculator
{
    function calculate(): array
    {
        $map = AnswersUtil::percentageWithValues($this->questionsHolder, $this->answersHolder, 6);
        unset($map['0']);
        $sum = AnswersUtil::sumValuesInDoubleMap($map);
        return array_merge($map, [
            'total' => $sum
        ]);
    }
}