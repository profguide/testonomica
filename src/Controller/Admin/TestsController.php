<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Test\TestSearchForm;
use App\Entity\Test;
use App\Form\TestType;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TestsController extends AbstractController
{
    public function __construct(
        private readonly TestSearchForm         $testSearchForm,
        private readonly TestRepository         $tests,
        private readonly EntityManagerInterface $em)
    {
    }

    #[Route('/admin/tests', name: 'admin.tests')]
    public function main(Request $request): Response
    {
        return $this->render('admin/tests/main.html.twig', [
            'pagination' => $this->testSearchForm->search($request)
        ]);
    }

    #[Route('/admin/tests/create', name: 'admin.tests.create')]
    public function create(Request $request): Response
    {
        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($test);
            $this->em->flush();

            $this->addFlash('success', 'Сохранено');

            return $this->redirectToRoute('admin.tests');
        }

        return $this->render('admin/tests/form.html.twig', [
            'title' => 'Создание теста',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/tests/edit?id={id}', name: 'admin.tests.edit')]
    public function edit(int $id, Request $request): Response
    {
        $test = $this->tests->findOneById($id) ?? $this->createNotFoundException();

        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($test);
            $this->em->flush();

            $this->addFlash('success', 'Сохранено');

            return $this->redirectToRoute('admin.tests.edit', ['id' => $id]);
        }

        return $this->render('admin/tests/form.html.twig', [
            'title' => $test->getName(),
            'form' => $form->createView(),
        ]);
    }
}