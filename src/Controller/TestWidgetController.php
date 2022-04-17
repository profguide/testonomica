<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PublicTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Виджет теста для Iframe.
 * Представляет собой HTML страницу с загруженными стилями теста и rect приложением.
 * Нет ни шапки, ни подвала. 100% ширина и высота отданы под тест.
 * React приложение является полноценным, содержит как сам тест, так и приём платежа.
 */
class TestWidgetController extends AbstractController implements HostAuthenticatedController
{
    private PublicTokenService $publicTokenService;

    public function __construct(PublicTokenService $publicTokenService)
    {
        $this->publicTokenService = $publicTokenService;
    }

    /**
     * @Route("/tests/w/{id}/", name="test.widget")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function widget(Request $request, int $id): Response
    {
        // показ результата по окончании теста
        $displayReport = self::boolParam($request, 'displayReport', true);

        // показывать ли результат при загрузке страницы если результат имеется
        $showResultAfterLoad = self::boolParam($request, 'showResultAfterLoad', true);

        $token = $request->get('token');
        // for development is http://127.0.0.1:8080
        $host = $request->getScheme() . '://' . $request->getHttpHost();
        return $this->render('tests/widget.html.twig', [
            'testId' => $id,
            'host' => $host,
            'token' => $token,
            'sid' => $this->sid($request),
            'displayReport' => $displayReport,
            'showResultAfterLoad' => $showResultAfterLoad,
        ]);
    }

    private function sid(Request $request): string
    {
        $sid = $request->get('sid');
        if (!$sid) {
            throw $this->createAccessDeniedException('Parameter sid is absent.');
        }
        return $sid;
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
}