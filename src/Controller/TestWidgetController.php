<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Test;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestWidgetController extends AbstractController implements HostAuthenticatedController
{
    private TestRepository $tests;

    public function __construct(TestRepository $tests)
    {
        $this->tests = $tests;
    }

    /**
     * @Route("/tests/widget/{id}/", name="test.widget")
     * Iframe
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function widget(Request $request, int $id): Response
    {
//        $test = $this->loadById($id);
        // null for free tests, no checking here
        $token = $request->get('token');
        // for development would be http://127.0.0.1:8080
        $host = $request->getScheme() . '://' . $request->getHttpHost();
        return $this->render('tests/widget.html.twig', [
            'testId' => $id,
            'host' => $host,
            'token' => $token
        ]);
    }

    // todo loadTestByKey like testometrika does
    private function loadById(int $id): Test
    {
        if (($test = $this->tests->findOneById($id)) == null) {
            self::createNotFoundException();
        }
        return $test;
    }
}