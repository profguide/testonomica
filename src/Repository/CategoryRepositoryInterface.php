<?php

namespace App\Repository;


use App\Entity\Category;

/**
 * @author: adavydov
 * @since: 20.10.2020
 */
interface CategoryRepositoryInterface
{
    function findOneBySlug(string $slug): ?Category;

    function findAll();

    function save(Category $category): Category;

    function update(Category $category): Category;
}