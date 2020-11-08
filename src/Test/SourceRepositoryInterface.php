<?php

namespace App\Test;

use App\Entity\Test;

/**
 * @author: adavydov
 * @since: 23.10.2020
 */
interface SourceRepositoryInterface
{
//    function parse();
//    function getQuestion(Test $test, $operationName);

    function getQuestion(Test $test, $id);

    function getNextQuestion(Test $test, $itemId);

    function getPrevQuestion(Test $test, $itemId);

    function getFirstQuestion(Test $test);

    function getTotalCount(Test $test);

    function getQuestionNumber(Test $test, Question $question);

    function getLastQuestion(Test $test): Question;
}