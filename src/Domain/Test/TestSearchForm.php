<?php

declare(strict_types=1);

namespace App\Domain\Test;


use App\Subscriber\Locale;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class TestSearchForm
{
    public function __construct(private EntityManagerInterface $em, private PaginatorInterface $paginator, private Locale $locale)
    {
    }

    public function search(Request $request, array $params = []): PaginationInterface
    {
        $builder = $this->em->createQueryBuilder()
            ->select('t')
            ->from('App:Test', 't')
            ->where('t.inList = 1')
            ->orderBy('t.id', 'DESC');

        if ($this->locale->getValue() === 'en') {
            $builder->andWhere()->where('t.activeEn > 0');
        } else {
            $builder->andWhere()->where('t.active > 0');
        }

        if (isset($params['author'])) {
            $builder->leftJoin('t.authors', 'a');
            $builder->andWhere('a.id=:authorId');
            $builder->setParameter(':authorId', $params['author']->getId());
        }

        return $this->paginator->paginate(
            $builder->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            30 /*limit per page*/
        );
    }
}