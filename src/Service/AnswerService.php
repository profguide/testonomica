<?php
/**
 * @author: adavydov
 * @since: 30.10.2020
 */

namespace App\Service;


use App\Entity\Answer;
use App\Entity\Test;
use App\Repository\AnswerRepository;
use App\Test\TestStatus;

class AnswerService implements AnswerServiceInterface
{
    /**@var AnswerRepository */
    private $repository;

    public function __construct(AnswerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Test $test
     * @param int $lastQuestionId
     * @return TestStatus
     */
    function status(Test $test, int $lastQuestionId): TestStatus
    {
        if (($lastAnswerId = $this->repository->getLastIdByTest($test)) == null) {
            return TestStatus::none();
        } elseif ($lastAnswerId == $lastQuestionId) {
            return TestStatus::finished();
        } else {
            return TestStatus::none();
        }
    }

    function getLastId(Test $test): ?int
    {
        return $this->repository->getLastIdByTest($test);
    }

    function save(Test $test, Answer $answer): void
    {
        $this->repository->save($test, $answer);
    }

    function getAll(Test $test): array
    {
        return $this->repository->getAll($test);
    }

    function clear(Test $test): void
    {
        $this->repository->clear($test);
    }

    public function count(Test $test): int
    {
        return $this->repository->count($test);
    }
}