<?php

namespace App\Repository;

use App\Entity\Test;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author: adavydov
 * @since: 20.10.2020
 */
class TestRepository extends ServiceEntityRepository implements TestRepositoryInterface
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
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
        /**@var Test $test */
        $test = parent::findOneBy(['id' => $id]);
        return $test;
    }

    function findOneBySlug(string $slug): ?Test
    {
        /**@var Test $test */
        $test = parent::findOneBy(['slug' => $slug]);
        return $test;
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
        $this->em->persist($test);
        $this->em->flush();
        return $test;
    }

    /**
     * @param Test $test
     * @return Test
     */
    public function update(Test $test): Test
    {
        $this->em->flush();
        return $test;
    }
}