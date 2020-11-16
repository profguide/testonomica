<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Repository;


use App\Entity\ProviderAccess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AccessRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, ProviderAccess::class);
    }

    public function save(ProviderAccess $providerAccess): ProviderAccess
    {
        $this->em->persist($providerAccess);
        $this->em->flush();
        return $providerAccess;
    }

    public function findOneByToken(string $token): ?ProviderAccess
    {
        /**@var ProviderAccess $providerAccess */
        $providerAccess = $this->findOneBy(['token' => $token]);
        return $providerAccess;
    }
}