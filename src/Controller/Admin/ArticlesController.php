<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Article\AdminArticlesSearchForm;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ArticlesController extends AbstractController
{
    public function __construct(
        private readonly AdminArticlesSearchForm $articlesSearchForm,
        private readonly ArticleRepository       $articles,
        private readonly EntityManagerInterface  $em)
    {
    }

    #[Route('/admin/articles', name: 'admin.articles')]
    public function main(Request $request): Response
    {
        return $this->render('admin/articles/main.html.twig', [
            'pagination' => $this->articlesSearchForm->search($request)
        ]);
    }

    #[Route('/admin/articles/create', name: 'admin.articles.create')]
    public function create(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'Сохранено');

            return $this->redirectToRoute('admin.articles');
        }

        return $this->render('admin/articles/form.html.twig', [
            'title' => 'Создание статьи',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/articles/edit?id={id}', name: 'admin.articles.edit')]
    public function edit(int $id, Request $request): Response
    {
        $article = $this->articles->findOneById($id) ?? throw $this->createNotFoundException();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'Сохранено');

            return $this->redirectToRoute('admin.articles.edit', ['id' => $id]);
        }

        return $this->render('admin/articles/form.html.twig', [
            'title' => $article->getName(),
            'form' => $form->createView(),
        ]);
    }
}