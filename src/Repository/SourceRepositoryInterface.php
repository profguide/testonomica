<?php

namespace App\Repository;

use App\Entity\Test;
use App\Test\Question;

/**
 * @author: adavydov
 * @since: 23.10.2020
 */
interface SourceRepositoryInterface
{
    function getQuestion(Test $test, $id);

    function getNextQuestion(Test $test, $itemId);

    function getPrevQuestion(Test $test, $itemId);

    function getFirstQuestion(Test $test);

    function getAllQuestions(Test $test): array;

    function getTotalCount(Test $test);

    function getQuestionNumber(Test $test, $question);

    function getLastQuestion(Test $test);
}