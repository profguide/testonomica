<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Test\Calculator;


use App\Test\AnswersHolder;
use App\Test\CalculatorInterface;

class TestCalculator implements CalculatorInterface
{
    function calculate(AnswersHolder $answersHolder): array
    {
        return $answersHolder->getAll();
    }
}