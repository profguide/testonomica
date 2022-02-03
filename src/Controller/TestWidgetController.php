<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Test;
use App\Repository\TestRepository;
use App\Service\PublicTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Тест в виджете
 *
 * Class TestWidgetController
 * @package App\Controller
 */
class TestWidgetController extends AbstractController implements HostAuthenticatedController
{
    private TestRepository $tests;

    private PublicTokenService $publicTokenService;

    public function __construct(TestRepository $tests, PublicTokenService $publicTokenService)
    {
        $this->tests = $tests;
        $this->publicTokenService = $publicTokenService;
    }

    /**
     * @Route("/tests/widget/{id}/", name="test.widget")
     * Страница должна быть загружена на сайте партнёра в Iframe.
     * Данная страница предоставляет тест полностью - оплата, приветствие, прогресс, результат.
     * Нет ни шапки, ни подвала. 100% ширина и высота отданы под тест.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function widget(Request $request, int $id): Response
    {
        $test = $this->getTest($id);

        // показывать ли результат при загрузке страницы если результат имеется
        $showResultAfterLoad = self::boolParam($request, 'showResultAfterLoad', true);

        $token = $request->get('token');
        // for development is http://127.0.0.1:8080
        $host = $request->getScheme() . '://' . $request->getHttpHost();
        return $this->render('tests/widget.html.twig', [
            'testId' => $id,
            'host' => $host,
            'token' => $token,
            'showResultAfterLoad' => $showResultAfterLoad
        ]);
    }

    /**
     * Allowed values: 1, 0, null
     *
     * @param Request $request
     * @param string $name
     * @param bool $default
     * @return bool
     */
    private static function boolParam(Request $request, string $name, bool $default): bool
    {
        if ($request->get($name) == null) {
            return $default;
        }
        return $request->get($name) == 1;
    }

//  todo loadTestByKey like testometrika does
    private function getTest(int $id): Test
    {
        $test = $this->tests->findOneById($id);
        if (!$test) {
            throw self::createNotFoundException();
        }
        return $test;
    }
}