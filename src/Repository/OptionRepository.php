<?php


namespace App\Repository;


use App\Entity\QuestionItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class OptionRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, QuestionItem::class);
    }

    public function save($result): QuestionItem
    {
        $this->em->persist($result);
        $this->em->flush();
        return $result;
    }
}