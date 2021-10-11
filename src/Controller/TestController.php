<?php

namespace App\Controller;

use App\Entity\Result;
use App\Entity\Test;
use App\Service\AccessService;
use App\Service\AnswerService;
use App\Service\CalculatorService;
use App\Service\CategoryService;
use App\Service\ResultService;
use App\Service\TestService;
use App\Test\ResultRenderer;
use App\Test\TestStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/tests", name="tests.")
 * Class TestsController
 * @package App\Controller
 */
class TestController extends AbstractController
{
    private TestService $testService;

    private CategoryService $categoryService;

    private ResultService $resultService;

    private AnswerService $answerService;

    private CalculatorService $calculatorService;

    private ResultRenderer $resultRenderer;

    private AccessService $accessService;


    public function __construct(
        TestService $testService,
        CategoryService $categoryService,
        AnswerService $answerService,
        ResultService $resultService,
        CalculatorService $calculatorService,
        ResultRenderer $resultRenderer,
        AccessService $accessService
    )
    {
        $this->testService = $testService;
        $this->categoryService = $categoryService;
        $this->answerService = $answerService;
        $this->resultService = $resultService;
        $this->calculatorService = $calculatorService;
        $this->resultRenderer = $resultRenderer;
        $this->accessService = $accessService;
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
     * Вьюшка с результатом тестирования
     * @Route("/result/{uuid}/", name="result")
     * @param string $uuid
     * @return Response
     */
    public function result(string $uuid): Response
    {
        $result = $this->loadResultByUuid($uuid);
        return $this->renderResult($result->getTest(), $result);
    }

    /**
     * Возвращает посчитанный результат теста в JSON, то есть то, что возвращает калькулятор
     * Логичнее разместить в TestApiStatelessController, но там нужна скорость,
     * а калькулятор - это дполнительные ветви зависимостей. Нужно провести исследование.
     * Есть еще вариант сделать отдельный контроллер для этого TestApiStatelessResultController
     *
     * @Route("/result-raw/{uuid}/", name="result_raw")
     * @param string $uuid
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function resultRaw(string $uuid, SerializerInterface $serializer)
    {
        $result = $this->loadResultByUuid($uuid);
        $response = new JsonResponse();
        $response->setJson($serializer->serialize($this->calculatorService->calculate($result), 'json'));
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $response;
    }

    /**
     * @Route("/view/{slug}/", name="view")
     * todo if test not free show payment widget or redirect to payment or redirect to presentation page
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function view(Request $request, string $slug): Response
    {
        $test = $this->loadBySlug($slug);
        if (($result = $this->resultService->getSessionResult($test)) != null) {
            return $this->renderResult($test, $result);
        }
        $this->assertActive($test);
        $this->assertAccess($test, $request); // todo remove, as payment on the API side.
        $status = $this->answerService->hasAnswers($test)
            ? TestStatus::progress()
            : TestStatus::none();
        // for development would be http://127.0.0.1:8080
        $host = $request->getScheme() . '://' . $request->getHttpHost();
        return $this->render('tests/view.html.twig', [
            'test' => $test,
            'category' => $test->getCatalog(),
            'status' => $status,
            'host' => $host
        ]);
    }

    private function loadBySlug(string $slug): Test
    {
        if (($test = $this->testService->findBySlug($slug)) == null) {
            self::createNotFoundException();
        }
        return $test;
    }

    private static function assertActive(Test $test)
    {
        if (!$test->isActive()) {
            throw new NotFoundHttpException();
        }
    }

    private function assertAccess(Test $test, Request $request)
    {
//        if ($this->getParameter('kernel.environment') === 'dev') {
//            return;
//        }
        // Определим является ли тест платным
        // Надо создать Service (пакет), ServiceTests (тесты в пакете) и Access (доступ к пакету (добавить поле service_id))
        if ($test->getSlug() !== 'proforientation-v2') {
            return;
        }
        // Из куки получать Access, находить Service по access.getService() и смотреть service.hasTest($test)
        $token = $this->accessService->getCookie($request);
        if ($token) {
            $access = $this->accessService->findOneByToken($token);
            if ($access) {
                $access->setUsed();
                $this->accessService->save($access);
                return;
            }
        }
        throw new AccessDeniedHttpException();
    }

    private function loadResultByUuid(string $uuid): Result
    {
        if (($result = $this->resultService->findByUuid($uuid)) == null) {
            throw new NotFoundHttpException();
        }
        return $result;
    }

    private function renderResult(Test $test, Result $result): Response
    {
        $data = array_merge([
            'test' => $test,
            'uuid' => $result->getUuid(),
            'status' => TestStatus::finished()
        ], $this->calculatorService->calculate($result));
        return $this->render('tests/result.html.twig', [
            'test' => $test,
            'status' => TestStatus::finished(),
            'uuid' => $result->getUuid(),
            'result' => $this->resultRenderer->render($test, $data)->getContent()
        ]);
    }
}