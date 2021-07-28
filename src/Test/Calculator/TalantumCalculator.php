<?php


namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class TalantumCalculator extends AbstractCalculator
{
    function calculate(): array
    {
        return AnswersUtil::percentageWithValues($this->questionsHolder, $this->answersHolder, 8);
    }
}