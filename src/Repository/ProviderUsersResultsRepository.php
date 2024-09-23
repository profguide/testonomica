<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProviderUser;
use App\Entity\ProviderUserResult;
use App\Entity\Result;
use App\Tests\Repository\ProviderUsersResultsRepositoryTest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @see ProviderUsersResultsRepositoryTest
 */
class ProviderUsersResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderUserResult::class);
    }

    public function hasRecordByResult(Result $result): bool
    {
        return $this->createQueryBuilder('l')
                ->select('count(l.id)')
                ->andWhere('l.result = :result')
                ->setParameter('result', $result->getNewId()->toBinary())
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function hasByResultAndUser(Result $result, ProviderUser $user): bool
    {
        return $this->createQueryBuilder('l')
                ->select('count(l.id)')
                ->andWhere('l.result = :result')
                ->andWhere('l.user = :user')
                ->setParameter('result', $result->getNewId()->toBinary())
                ->setParameter('user', $user->getId()->toBinary())
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function hasAnotherUserAttached(Result $result, ProviderUser $user): bool
    {
        return $this->createQueryBuilder('l')
                ->select('count(l.id)')
                ->andWhere('l.result = :result')
                ->andWhere('l.user != :user')
                ->setParameter('result', $result->getNewId()->toBinary())
                ->setParameter('user', $user->getId()->toBinary())
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }
}
