<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/doc", name="doc.")
 */
class DocController extends AbstractController
{
    /**
     * @Route("/", name="doc.index")
     */
    public function actionIndex(): Response
    {
        return $this->render('doc/index.html.twig');
    }
}