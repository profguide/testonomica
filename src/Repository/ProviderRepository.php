<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ProviderRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, Provider::class);
    }

    public function findBySlug(string $slug): ?Provider
    {
        /**@var Provider $provider */
        return $this->findOneBy(['slug' => $slug]);
    }

    public function getByToken(string $token): ?Provider
    {
        return $this->findOneBy(['token' => $token]);
    }
}