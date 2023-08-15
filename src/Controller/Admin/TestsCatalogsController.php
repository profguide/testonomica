<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TestsCatalogsController extends AbstractController
{
    #[Route('/admin/tests-catalogs', name: 'admin.tests_catalogs')]
    public function main(): Response
    {
        return $this->render('admin/tests_catalogs/main.html.twig');
    }
}