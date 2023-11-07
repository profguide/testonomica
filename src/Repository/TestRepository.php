<?php

namespace App\Repository;

use App\Entity\Test;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TestRepository extends ServiceEntityRepository implements TestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Test::class);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function findAllActive(): array
    {
        return parent::findBy(['active' => 1]);
    }

    function findAllActiveList(): array
    {
        return parent::findBy(['active' => 1, 'inList' => 1]);
    }

    public function findAllPerPage(int $page, int $limit = 10): array
    {
        return parent::findBy([
            'offset' => $page * $limit,
            'limit' => $limit]);
    }

    function findOneById(int $id): ?Test
    {
        return parent::findOneBy(['id' => $id]);
    }

    function findOneBySlug(string $slug): ?Test
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function findAllByCatalog(int $id, $page, int $limit = 10): array
    {
        return parent::findBy([
            'catalogId' => $id,
            'offset' => $page * $limit,
            'limit' => $limit]);
    }

    /**
     * @param Test $test
     * @return Test
     */
    public function save(Test $test): Test
    {
        $this->getEntityManager()->persist($test);
        $this->getEntityManager()->flush();
        return $test;
    }

    /**
     * @param Test $test
     * @return Test
     */
    public function update(Test $test): Test
    {
        $this->getEntityManager()->flush();
        return $test;
    }

    public function getWithSearchQueryBuilder(?string $getQueryString)
    {
        $dql = "SELECT t FROM App:Test t";
        return $this->getEntityManager()->createQuery($dql);
//        return $this->em->createQueryBuilder()->getQuery();
    }
}