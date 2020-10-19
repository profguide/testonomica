<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    /**
     * @Route("/tests", name="tests")
     */
    public function index()
    {
        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
        ]);
    }
}
