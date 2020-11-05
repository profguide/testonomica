<?php

namespace App\Controller;

use App\Entity\Test;
use App\Service\CalculatorService;
use App\Service\CategoryService;
use App\Service\ResultService;
use App\Service\TestService;
use App\Test\ResultUtil;
use App\Test\TestStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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

    /**@var CalculatorService */
    private $calculatorService;

    public function __construct(
        TestService $testService,
        CategoryService $categoryService,
        ResultService $resultService,
        CalculatorService $calculatorService)
    {
        $this->testService = $testService;
        $this->categoryService = $categoryService;
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
        $test = $result->getTest();
        $resultData = $this->calculatorService->calculate($test, $result);
        return $this->render('tests/result/' . ResultUtil::resolveViewName($test) . '.html.twig',
            array_merge(['test' => $test], $resultData));
    }

    /**
     * @Route("/{categorySlug}/{slug}/", name="view")
     * @param string $categorySlug
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(string $categorySlug, string $slug)
    {
        $test = $this->loadBySlug($slug);
        self::assertActive($test);
        $category = $test->getCatalog();
        self::assertTestInCategory($test, $categorySlug);
        return $this->render('tests/view.html.twig', [
            'test' => $test,
            'category' => $category,
            'status' => TestStatus::none(), // <<
        ]);
    }

    private function loadBySlug(string $slug): Test
    {
        if (($test = $this->testService->findBySlug($slug)) == null) {
            throw new NotFoundHttpException();
        }
        return $test;
    }

    private static function assertTestInCategory(Test $test, string $categorySlug)
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

    private function loadResultByUuid(string $uuid)
    {
        if (($result = $this->resultService->findByUuid($uuid)) == null) {
            throw new NotFoundHttpException();
        }
        return $result;
    }
}