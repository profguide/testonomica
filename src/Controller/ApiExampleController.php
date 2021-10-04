<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiExampleController extends AbstractController
{
    /**
     * @Route("example/api", name="example_api")
     */
    public function actionIndex(): Response
    {
        return $this->render('example/api.html.twig');
    }
}