<?php

namespace App\Controller;

use App\Entity\Result;
use App\Entity\Test;
use App\Service\AnswerService;
use App\Service\CalculatorService;
use App\Service\CategoryService;
use App\Service\ResultService;
use App\Service\TestService;
use App\Test\ResultUtil;
use App\Test\TestStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tests", name="tests.")
 * Class TestsController
 * @package App\Controller
 */
class TestController extends AbstractController
{
    /**@var TestService */
    private $testService;

    /**@var CategoryService */
    private $categoryService;

    /**@var ResultService */
    private $resultService;

    /**@var AnswerService */
    private $answerService;

    /**@var CalculatorService */
    private $calculatorService;

    public function __construct(
        TestService $testService,
        CategoryService $categoryService,
        AnswerService $answerService,
        ResultService $resultService,
        CalculatorService $calculatorService)
    {
        $this->testService = $testService;
        $this->categoryService = $categoryService;
        $this->answerService = $answerService;
        $this->resultService = $resultService;
        $this->calculatorService = $calculatorService;
    }

    /**
     * @Route("/", name="index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $tests = $this->testService->findAllActive();
        return $this->render('tests/index.html.twig', [
            'tests' => $tests,
        ]);
    }

    /**
     * @Route("/result/{uuid}/", name="result")
     * @param string $uuid
     * @return Response
     */
    public function result(string $uuid)
    {
        $result = $this->loadResultByUuid($uuid);
        return $this->renderResult($result->getTest(), $result);
    }

    /**
     * @Route("/{categorySlug}/{slug}/", name="view")
     * @param Request $request
     * @param string $categorySlug
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(Request $request, string $categorySlug, string $slug)
    {
        $test = $this->loadBySlug($slug);
        if (($result = $this->resultService->getSessionResult($test)) != null) {
            return $this->renderResult($test, $result);
        }
        $this->assertUrl($test, $categorySlug);
        $this->assertActive($test);
        $this->assertAccess($test, $request);
        $status = $this->answerService->hasAnswers($test)
            ? TestStatus::progress()
            : TestStatus::none();
        return $this->render('tests/view.html.twig', [
            'test' => $test,
            'category' => $test->getCatalog(),
            'status' => $status,
        ]);
    }

    private function loadBySlug(string $slug): Test
    {
        if (($test = $this->testService->findBySlug($slug)) == null) {
            throw new NotFoundHttpException();
        }
        return $test;
    }

    private static function assertUrl(Test $test, string $categorySlug)
    {
        if ($test->getCatalog()->getSlug() !== $categorySlug) {
            throw new NotFoundHttpException();
        }
    }

    private static function assertActive(Test $test)
    {
        if (!$test->isActive()) {
            throw new NotFoundHttpException();
        }
    }

    private function assertAccess(Test $test, Request $request)
    {
        if ($this->getParameter('kernel.environment') === 'dev') {
            return;
        }
        // какой-то резолвер надо
        if ($test->getSlug() !== 'test_2') {
            return;
        }
        // а тут проверять доступ к конкретной услуге как-то надо
        if (!empty($request->cookies->get('access'))) {
            return;
        }
        throw new AccessDeniedHttpException();
    }

    private function loadResultByUuid(string $uuid)
    {
        if (($result = $this->resultService->findByUuid($uuid)) == null) {
            throw new NotFoundHttpException();
        }
        return $result;
    }

    private function renderResult(Test $test, Result $result)
    {
        $resultData = $this->calculatorService->calculate($test, $result);
        return $this->render('tests/result/' . ResultUtil::resolveViewName($test) . '.html.twig',
            array_merge([
                'test' => $test,
                'uuid' => $result->getUuid(),
                'status' => TestStatus::finished()
            ], $resultData));
    }
}