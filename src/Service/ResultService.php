<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Service;


use App\Entity\Result;
use App\Repository\ResultRepository;

class ResultService
{
    /**@var ResultRepository */
    private $repository;

    public function __construct(ResultRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save(Result $result): Result
    {
        return $this->repository->save($result);
    }

    public function findByUuid(string $uuid): ?Result
    {
        return $this->repository->findByUuid($uuid);
    }
}