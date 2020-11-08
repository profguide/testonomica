<?php

namespace App\Service;


use App\Entity\Test;
use App\Test\Question;
use App\Test\SourceRepositoryInterface;

/**
 * @author: adavydov
 * @since: 25.10.2020
 */
class TestSourceService
{
    /**@var SourceRepositoryInterface */
    private $repository;

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

    public function getTotalCount(Test $test)
    {
        return $this->repository->getTotalCount($test);
    }

    public function getQuestionNumber(Test $test, Question $question)
    {
        return $this->repository->getQuestionNumber($test, $question);
    }
}