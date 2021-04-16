<?php

namespace App\Repository;

use App\Entity\AnalysisCondition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AnalysisConditionRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, AnalysisCondition::class);
    }

    public function save($entity): AnalysisCondition
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
}