<?php


namespace App\Repository;


use App\Entity\ArticleCatalog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ArticleCatalogRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, ArticleCatalog::class);
    }

    public function findBySlug(string $slug): ?ArticleCatalog
    {
        /**@var ArticleCatalog $catalog */
        $catalog = $this->findOneBy(['slug' => $slug]);
        return $catalog;
    }
}