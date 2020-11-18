<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Repository;


use App\Entity\Access;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AccessRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, Access::class);
    }

    public function save(Access $providerAccess): Access
    {
        $this->em->persist($providerAccess);
        $this->em->flush();
        return $providerAccess;
    }

    public function findOneByToken(string $token): ?Access
    {
        /**@var Access $providerAccess */
        $providerAccess = $this->findOneBy(['token' => $token]);
        return $providerAccess;
    }
}