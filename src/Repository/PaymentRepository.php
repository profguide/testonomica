<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Repository;


use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PaymentRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, Payment::class);
    }
}