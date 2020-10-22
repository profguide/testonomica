<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Test;

/**
 * @author: adavydov
 * @since: 20.10.2020
 */
interface TestRepositoryInterface
{
    function findAll(): array;

    function findAllActive(): array;

    function findAllPerPage(int $page, int $limit): array;

    function findOneById(int $id): ?Test;

    function findOneBySlug(string $slug): ?Test;

    function findAllByCatalog(int $id, int $page, int $limit): array;

    function save(Test $test): Test;

    function update(Test $test): Test;
}