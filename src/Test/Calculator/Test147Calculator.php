<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;

class Test147Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $groups = $this->questionsHolder->byGroups();
        $map = ['a' => 0, 'b' => 0, 'c' => 0, 'd' => 0];
        foreach ($groups as $name => $questions) {
            $map[$name] = AnswersUtil::sum(new QuestionsHolder($questions), $this->answersHolder);
        }
        return [
            'rt' => $rt = $map['a'] + $map['b'],
            'lt' => $lt = $map['c'] + $map['d'],
            'scale' => [
                'rt' => round($rt * 100 / 80),
                'lt' => round($lt * 100 / 80),
            ]
        ];
    }
}