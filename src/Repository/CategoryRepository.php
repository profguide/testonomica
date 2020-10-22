<?php

namespace App\Repository;


use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author: adavydov
 * @since: 20.10.2020
 */
class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($registry, Category::class);
    }

    function findOneBySlug(string $slug): ?Category
    {
        /**@var Category $category */
        $category = parent::findBy(['slug' => $slug]);
        return $category;
    }

    function save(Category $category): Category
    {
        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }

    function update(Category $category): Category
    {
        $this->em->flush();
        return $category;
    }
}