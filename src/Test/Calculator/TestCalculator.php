<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Test\Calculator;


use App\Test\AnswersHolder;
use App\Test\CalculatorInterface;
use App\Test\QuestionsHolder;

class TestCalculator implements CalculatorInterface
{
    function calculate(AnswersHolder $answersHolder, QuestionsHolder $questionsHolder): array
    {
        return $answersHolder->getAll();
    }
}