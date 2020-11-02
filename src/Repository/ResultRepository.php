<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Repository;


use App\Entity\Result;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ResultRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, Result::class);
    }

    public function save($result): Result
    {
        $this->em->persist($result);
        $this->em->flush();
        return $result;
    }

    public function findByUuid(string $uuid): ?Result
    {
        /**@var Result $result */
        $result = $this->findOneBy(['uuid' => $uuid]);
        return $result;
    }
}