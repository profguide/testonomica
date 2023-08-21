<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Author\AuthorSearchForm;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AuthorsController extends AbstractController
{
    public function __construct(
        private readonly AuthorSearchForm       $searchForm,
        private readonly AuthorRepository       $authors,
        private readonly EntityManagerInterface $em)
    {
    }

    #[Route('/admin/authors', name: 'admin.authors')]
    public function main(Request $request): Response
    {
        return $this->render('admin/authors/main.html.twig', [
            'pagination' => $this->searchForm->search($request)
        ]);
    }

    #[Route('/admin/authors/create', name: 'admin.authors.create')]
    public function create(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($author);
            $this->em->flush();

            $this->addFlash('success', 'Сохранено');

            return $this->redirectToRoute('admin.authors');
        }

        return $this->render('admin/authors/form.html.twig', [
            'title' => 'Добавление автора',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/authors/edit?id={id}', name: 'admin.authors.edit')]
    public function edit(int $id, Request $request): Response
    {
        /**@var $author Author */
        $author = $this->authors->findOneById($id) ?? throw $this->createNotFoundException();

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($author);
            $this->em->flush();

            $this->addFlash('success', 'Сохранено');

            return $this->redirectToRoute('admin.authors.edit', ['id' => $id]);
        }

        return $this->render('admin/authors/form.html.twig', [
            'title' => $author->getName(),
            'form' => $form->createView(),
        ]);
    }
}