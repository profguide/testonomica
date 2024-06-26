<?php
/**
 * @author: adavydov
 * @since: 18.11.2020
 */

namespace App\Repository;


use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ServiceRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, Service::class);
    }

    public function getById(int $id): Service
    {
        /**@var Service $service */
        $service = $this->findOneBy(['id' => $id]);
        if ($service) {
            return $service;
        }
        throw new \DomainException("Service not found (id: \"$id\").");
    }

    public function getOneBySlug(string $slug): ?Service
    {
        /**@var Service $service */
        $service = $this->findOneBy(['slug' => $slug]);
        return $service;
    }
}