<?php
/**
 * @author: adavydov
 * @since: 20.10.2020
 */

namespace App\Service;


use App\Entity\Category;
use App\Repository\CategoryRepositoryInterface;

class CategoryService
{
    private $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function create(string $slug, string $name): Category
    {
        $category = new Category($slug, $name);
        return $this->repository->save($category);
    }

    public function update(Category $category): Category
    {
        return $this->repository->update($category);
    }
}