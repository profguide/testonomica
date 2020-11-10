<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Repository;


use App\Entity\Provider;
use App\Entity\ProviderPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ProviderPaymentRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, ProviderPayment::class);
    }

    function findByToken(string $token): ?ProviderPayment
    {
        /**@var ProviderPayment $o */
        $o = parent::findOneBy(['token' => $token]);
        return $o;
    }

    public function findOneByProviderAndUser(Provider $provider, string $user): ?ProviderPayment
    {
        /**@var ProviderPayment $o */
        $o = parent::findOneBy(['provider_id' => $provider->getId(), 'user' => $user]);
        return $o;
    }
}