<?php
/**
 * @author: adavydov
 * @since: 30.10.2020
 */

namespace App\Service;


use App\Entity\Answer;
use App\Entity\Test;
use App\Test\TestStatus;

interface AnswerServiceInterface
{
    function hasAnswers(Test $test): bool;

    function getLastId(Test $test): ?int;

    function save(Test $test, Answer $answer): void;

    function getAll(Test $test): array;

    function clear(Test $test): void;
}