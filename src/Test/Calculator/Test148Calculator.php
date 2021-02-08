<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;

class Test148Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $groups = $this->questionsHolder->byGroups();
        $sums = ['a' => 0, 'b' => 0];
        foreach ($groups as $name => $questions) {
            $sums[$name] = AnswersUtil::sum(new QuestionsHolder($questions), $this->answersHolder);
        }
        return [
            'sum' => $sum = $sums['a'] + $sums['b'],
            'scale' => round($sum * 100 / 80),
        ];
    }
}