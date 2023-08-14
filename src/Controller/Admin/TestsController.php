<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Test\TestSearchForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TestsController extends AbstractController
{
    public function __construct(private readonly TestSearchForm $testSearchForm)
    {
    }

    #[Route('/admin/tests', name: 'admin.tests')]
    public function main(Request $request): Response
    {
        return $this->render('admin/tests/main.html.twig', [
            'pagination' => $this->testSearchForm->search($request)
        ]);
    }

    #[Route('/admin/tests/edit?id={id}', name: 'admin.tests_edit')]
    public function edit(int $id): Response
    {
        echo 'edit ' . $id;
        die();
    }
}