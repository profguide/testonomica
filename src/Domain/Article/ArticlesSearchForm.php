<?php

declare(strict_types=1);

namespace App\Domain\Article;

use App\Entity\ArticleCatalog;
use App\Subscriber\Locale;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final class ArticlesSearchForm
{
    public function __construct(private EntityManagerInterface $em, private PaginatorInterface $paginator, private Locale $locale)
    {
    }

    public function search(Request $request, ?ArticleCatalog $catalog): PaginationInterface
    {
        $builder = $this->em->createQueryBuilder();
        $builder->select('a');
        $builder->from('App:Article', 'a');
        if ($catalog) {
            $builder->andWhere()->where('a.catalog=:catalog');
            $builder->setParameter(':catalog', $catalog);
        }
        if ($this->locale->getValue() === 'en') {
            $builder->andWhere()->where('a.activeEn > 0');
        } else {
            $builder->andWhere()->where('a.active > 0');
        }
        $builder->orderBy('a.id', 'DESC');

        return $this->paginator->paginate(
            $builder->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
    }
}