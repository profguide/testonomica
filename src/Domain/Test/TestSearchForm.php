<?php

declare(strict_types=1);

namespace App\Domain\Test;


use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class TestSearchForm
{
    public function __construct(private EntityManagerInterface $em, private PaginatorInterface $paginator)
    {
    }

    public function search(Request $request, array $params = []): PaginationInterface
    {
        $query = $this->em->createQueryBuilder()
            ->select('t')
            ->from('App:Test', 't')
            ->where('t.inList = 1')
            ->orderBy('t.id', 'DESC');

        if (isset($params['author'])) {
            $query->leftJoin('t.authors', 'a');
            $query->andWhere('a.id=:authorId');
            $query->setParameter(':authorId', $params['author']->getId());
        }

        return $this->paginator->paginate(
            $query->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            30 /*limit per page*/
        );
    }
}