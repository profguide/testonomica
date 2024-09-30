<?php

namespace App\Service;

use App\Entity\Question;
use App\Entity\Test;
use App\Exception\ProgressValidationException;
use App\Repository\SourceRepositoryInterface;
use App\Test\Progress\Progress;

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

    /**
     * @param Test $test
     * @return Question[]
     */
    function getAll(Test $test): array
    {
        return $this->repository->getAllQuestions($test);
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

    public function getInstruction(Test $test): ?string
    {
        return $this->repository->getInstruction($test);
    }

    /***
     * @param Test $test
     * @param Progress $progress
     */
    public function validateRawAnswers(Test $test, Progress $progress): void
    {
        if ($this->getTotalCount($test) != count($progress->answers)) {
            throw new ProgressValidationException('Not matching answers count.');
        }
    }
}