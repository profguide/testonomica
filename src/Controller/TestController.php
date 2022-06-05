<?php

namespace App\Controller;

use App\Entity\Result;
use App\Entity\Test;
use App\Service\AnswerService;
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
    private TestService $testService;

    private ResultService $resultService;

    private AnswerService $answerService;

    private CalculatorService $calculatorService;

    private ResultRenderer $resultRenderer;

    public function __construct(
        TestService $testService,
        AnswerService $answerService,
        ResultService $resultService,
        CalculatorService $calculatorService,
        ResultRenderer $resultRenderer
    )
    {
        $this->testService = $testService;
        $this->answerService = $answerService;
        $this->resultService = $resultService;
        $this->calculatorService = $calculatorService;
        $this->resultRenderer = $resultRenderer;
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
     * @return Response
     */
    public function index(): Response
    {
        $tests = $this->testService->findAllActiveList();
        return $this->render('tests/index.html.twig', [
            'tests' => $tests,
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

        $format = new ViewFormat(ViewFormat::HTML);
        return $this->render('tests/result.html.twig', [
            'test' => $test,
            'status' => TestStatus::finished(),
            'uuid' => $result->getUuid(),
            'result' => $this->resultRenderer->render($test, $data, $format)->getContent()
        ]);
    }

    private function getTest(string $slug): Test
    {
        $test = $this->testService->findBySlug($slug);
        if (!$test || !$test->isActive()) {
            self::createNotFoundException();
        }
        return $test;
    }

    private function getResult(string $uuid): Result
    {
        $result = $this->resultService->findByUuid($uuid);
        if (!$result) {
            self::createNotFoundException();
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