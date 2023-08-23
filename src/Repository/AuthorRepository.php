<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

final class AuthorRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, Author::class);
    }

    public function save($entity): Author
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    public function findOneBySlug(string $slug): ?Author
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}