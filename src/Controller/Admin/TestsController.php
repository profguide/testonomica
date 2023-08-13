<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TestsController extends AbstractController
{
    #[Route('/admin/tests', name: 'admin.tests')]
    public function main(): Response
    {
        return $this->render('admin/tests/main.html.twig');
    }
}