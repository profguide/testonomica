<?php

declare(strict_types=1);

namespace App\Domain\Author;


use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class AuthorSearchForm
{
    public function __construct(private EntityManagerInterface $em, private PaginatorInterface $paginator)
    {
    }

    public function search(Request $request): PaginationInterface
    {
        $query = $this->em->createQuery("SELECT a FROM App:Author a ORDER BY a.name");

        return $this->paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            30 /*limit per page*/
        );
    }
}