<?php

namespace App\Repository;

use App\Entity\ProviderUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProviderUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderUser::class);
    }

    public function findOneByProviderAndExtUserId(\App\Entity\Provider $provider, string $extUserId)
    {
        return $this->findOneBy([
            'provider' => $provider->getId(),
            'extUserId' => $extUserId
        ]);
    }
}
