<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Test;

class DBSourceRepository implements SourceRepositoryInterface
{
    private QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function getQuestion(Test $test, $id): Question
    {
        return $this->questionRepository->findOneById($id);
    }

    public function getNextQuestion(Test $test, $id): ?Question
    {
        return $this->questionRepository->findOneNext($id, $test->getId());
    }

    public function getPrevQuestion(Test $test, $id): ?Question
    {
        return $this->questionRepository->findOnePrev($id, $test->getId());
    }

    public function getFirstQuestion(Test $test): Question
    {
        return $this->questionRepository->findFirstByTestId($test->getId());
    }

    public function getLastQuestion(Test $test): Question
    {
        return $this->questionRepository->findLastByTestId($test->getId());
    }

    public function getQuestionNumber(Test $test, $id): int
    {
        return $this->questionRepository->getPosition($id, $test->getId());
    }

    public function getAllQuestions(Test $test): array
    {
        return $this->questionRepository->findAllByTestId($test->getId());
    }

    public function getTotalCount(Test $test): int
    {
        return $this->questionRepository->countByTestId($test->getId());
    }

    public function getInstruction(Test $test): ?string
    {
        // not supported yet
        return null;
    }
}