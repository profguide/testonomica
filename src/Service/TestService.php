<?php

namespace App\Service;


use App\Entity\Test;
use App\Repository\TestRepositoryInterface;

/**
 * @author: adavydov
 * @since: 20.10.2020
 */
class TestService
{
    private $repository;

    public function __construct(TestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(Test $test): Test
    {
        return $this->repository->save($test);
    }

    public function update(Test $test): Test
    {
        return $this->repository->update($test);
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findAllActiveList(): array
    {
        return $this->repository->findAllActiveList();
    }

    public function findBySlug(string $slug): ?Test
    {
        return $this->repository->findOneBySlug($slug);
    }

    public function findById($id): ?Test
    {
        return $this->repository->findOneById($id);
    }
}