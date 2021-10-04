<?php

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Test;
use App\Repository\SourceRepositoryInterface;

/**
 * @author: adavydov
 * @since: 25.10.2020
 */
class TestSourceService
{
    private SourceRepositoryInterface $repository;

    public function __construct(SourceRepositoryInterface $sourceParser)
    {
        $this->repository = $sourceParser;
    }

    public function getQuestion(Test $test, $id): Question
    {
        return $this->repository->getQuestion($test, $id);
    }

    function getNextQuestion(Test $test, $itemId): ?Question
    {
        return $this->repository->getNextQuestion($test, $itemId);
    }

    function getPrevQuestion(Test $test, $itemId): ?Question
    {
        return $this->repository->getPrevQuestion($test, $itemId);
    }

    function getFirstQuestion(Test $test): Question
    {
        return $this->repository->getFirstQuestion($test);
    }

    function getLastQuestion(Test $test): Question
    {
        return $this->repository->getLastQuestion($test);
    }

    public function getTotalCount(Test $test): int
    {
        return $this->repository->getTotalCount($test);
    }

    public function getQuestionNumber(Test $test, $id): int
    {
        return $this->repository->getQuestionNumber($test, $id);
    }

    /***
     * @param Test $test
     * @param Answer[] $answers
     */
    public function validateRawAnswers(Test $test, array $answers)
    {
        if ($this->getTotalCount($test) != count($answers)) {
            throw new \LogicException('Not matching answers count.');
        }
        // validate ids
        // validate values?
    }
}