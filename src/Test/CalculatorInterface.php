<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Test;


interface CalculatorInterface
{
    function calculate(AnswersHolder $answersHolder, QuestionsHolder $questionsHolder): array;
}