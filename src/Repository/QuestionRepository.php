<?php


namespace App\Repository;


use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class QuestionRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($registry, Question::class);
    }

    public function save($result): Question
    {
        $this->em->persist($result);
        $this->em->flush();
        return $result;
    }

    public function findOneById($id): ?Question
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Finds next Question
     *
     * @param $id
     * @param $testId
     * @return Question|null
     */
    public function findOneNext($id, int $testId): ?Question
    {
        return $this->em
            ->createQuery('select q from App\Entity\Question q where q.test=:test and q.id>:id order by q.id')
            ->setParameter('test', $testId)
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * Finds previous Question
     *
     * @param $id
     * @param int $testId
     * @return Question|null
     */
    public function findOnePrev($id, int $testId): ?Question
    {
        return $this->em
            ->createQuery('select q from App\Entity\Question q where q.test=:test and q.id<:id order by q.id DESC')
            ->setParameter('test', $testId)
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function findFirstByTestId(int $testId): ?Question
    {
        return $this->em
            ->createQuery('select q from App\Entity\Question q where q.test=:test order by q.id')
            ->setParameter('test', $testId)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function findLastByTestId(int $testId): ?Question
    {
        return $this->em
            ->createQuery('select q from App\Entity\Question q where q.test=:test order by q.id DESC')
            ->setParameter('test', $testId)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function getPosition($id, int $testId): int
    {
        return $this->createQueryBuilder('q')
                ->select('count(q.id)')
                ->andWhere('q.test=:test')
                ->andWhere('q.id<:id')
                ->setParameter('test', $testId)
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleScalarResult() + 1;
    }

    public function findAllByTestId(int $testId): array
    {
        return $this->findBy(['test' => $testId]);
    }

    public function countByTestId(int $testId): int
    {
        return $this->count(['test' => $testId]);
    }
}