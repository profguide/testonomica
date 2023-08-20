<?php

namespace App\Controller;

use App\Domain\Test\TestSearchForm;
use App\Entity\Result;
use App\Entity\Test;
use App\Service\CalculatorService;
use App\Service\ResultService;
use App\Service\TestService;
use App\Test\ResultRenderer;
use App\Test\TestStatus;
use App\Test\ViewFormat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tests", name="tests.")
 */
class TestController extends AbstractController
{
    public function __construct(
        private readonly TestSearchForm    $testSearchForm,
        private readonly TestService       $testService,
        private readonly ResultService     $resultService,
        private readonly CalculatorService $calculatorService,
        private readonly ResultRenderer    $resultRenderer
    )
    {
    }

    /**
     * @Route("/iframe/", name="iframe")
     * @return Response
     */
    public function iframe(): Response
    {
        return $this->render('tests/iframe.html.twig');
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('tests/index.html.twig', [
            'pagination' => $this->testSearchForm->search($request)
        ]);
    }

    /**
     * @Route("/view/{slug}/", name="view")
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function view(Request $request, string $slug): Response
    {
        $test = $this->getTest($slug);
        return $this->render('tests/view.html.twig', [
            'test' => $test,
            'category' => $test->getCatalog(),
            'host' => self::host($request)
        ]);
    }

    /**
     * @Route("/result/{uuid}/", name="result")
     * @param string $uuid
     * @return Response
     */
    public function result(string $uuid): Response
    {
        $result = $this->getResult($uuid);
        $test = $result->getTest();
        $data = array_merge([
            'test' => $test,
            'uuid' => $result->getUuid(),
            'status' => TestStatus::finished()
        ], $this->calculatorService->calculate($result));

        return $this->render('tests/result.html.twig', [
            'test' => $test,
            'status' => TestStatus::finished(),
            'uuid' => $result->getUuid(),
            'result' => $this->resultRenderer->render($test, $data, new ViewFormat(ViewFormat::HTML))->getContent()
        ]);
    }

    private function getTest(string $slug): Test
    {
        $test = $this->testService->findBySlug($slug);
        if (!$test || !$test->isActive()) {
            throw self::createNotFoundException();
        }
        return $test;
    }

    private function getResult(string $uuid): Result
    {
        $result = $this->resultService->findByUuid($uuid);
        if (!$result) {
            throw self::createNotFoundException();
        }
        return $result;
    }

    private static function host(Request $request): string
    {
        return $request->getScheme() . '://' . $request->getHttpHost();
    }

//    private function assertAccess(Test $test, Request $request)
//    {
////        if ($this->getParameter('kernel.environment') === 'dev') {
////            return;
////        }
//        // Определим является ли тест платным
//        // Надо создать Service (пакет), ServiceTests (тесты в пакете) и Access (доступ к пакету (добавить поле service_id))
//        if ($test->getSlug() !== 'proforientation-v2') {
//            return;
//        }
//        // Из куки получать Access, находить Service по access.getService() и смотреть service.hasTest($test)
//        $token = $this->accessService->getCookie($request);
//        if ($token) {
//            $access = $this->accessService->findOneByToken($token);
//            if ($access) {
//                $access->setUsed();
//                $this->accessService->save($access);
//                return;
//            }
//        }
//        throw new AccessDeniedHttpException();
//    }
}