<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Test;

/**
 * @author: adavydov
 * @since: 23.10.2020
 */
interface SourceRepositoryInterface
{
    function getQuestion(Test $test, $id): Question;

    function getNextQuestion(Test $test, $id): ?Question;

    function getPrevQuestion(Test $test, $id): ?Question;

    function getFirstQuestion(Test $test): Question;

    function getLastQuestion(Test $test): Question;

    function getAllQuestions(Test $test): array;

    function getTotalCount(Test $test): int;

    function getQuestionNumber(Test $test, $id): int;

    public function getInstruction(Test $test): ?string;
}