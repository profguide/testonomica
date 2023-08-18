<?php

declare(strict_types=1);

namespace App\Domain\Test;


use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class AdminTestSearchForm
{
    public function __construct(private EntityManagerInterface $em, private PaginatorInterface $paginator)
    {
    }

    public function search(Request $request): PaginationInterface
    {
        $query = $this->em->createQuery("SELECT t FROM App:Test t ORDER BY t.id");

        return $this->paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
    }
}