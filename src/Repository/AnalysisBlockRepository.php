<?php

namespace App\Repository;

use App\Entity\AnalysisBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AnalysisBlockRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, AnalysisBlock::class);
    }

    public function save($entity): AnalysisBlock
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
}