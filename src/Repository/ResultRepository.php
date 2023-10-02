<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Repository;


use App\Entity\Result;
use App\Test\Result\ResultKey;
use App\Test\Result\ResultUuidKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ResultRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, Result::class);
    }

    public function save($result): Result
    {
        $this->em->persist($result);
        $this->em->flush();
        return $result;
    }

    /**
     * @param string $uuid
     * @return Result|null
     * @deprecated, use findByKey
     */
    public function findByUuid(string $uuid): ?Result
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    public function findByKey(ResultKey $key): ?Result
    {
        if (get_class($key) === ResultUuidKey::class) {
            return $this->find($key->getValue());
        } else {
            return $this->findOneBy(['uuid' => $key->getValue()]);
        }
    }
}