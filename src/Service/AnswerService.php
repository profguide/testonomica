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

    public function hasAnswers(Test $test): bool
    {
        return $this->repository->getLastIdByTest($test) != null;
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