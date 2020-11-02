<?php

namespace App\Controller;

use App\Entity\Test;
use App\Service\CalculatorService;
use App\Service\CategoryService;
use App\Service\ResultService;
use App\Service\TestService;
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
        return $this->render('tests/result.html.twig', array_merge([
            'test' => $test,
        ], $this->calculatorService->calculate($test, $result)));
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
        return $this->resultService->findByUuid($uuid);
    }
}

///**
// * @Route("/create/", name="create")
// * @param Request $request
// * @return \Symfony\Component\HttpFoundation\Response
// */
//public function create(Request $request)
//{
//    $test = Test::initDefault();
//    $form = $this->createForm(TestType::class, $test);
//    $form->handleRequest($request);
//    if ($form->isSubmitted() && $form->isValid()) {
//        $test = $form->getData();
//        $test = $this->testService->create($test);
//        $this->addFlash('success', 'Saved!');
//        return $this->redirect($this->generateUrl('tests.update', ['id' => $test->getId()]));
//    }
//    return $this->render('tests/form.html.twig', [
//        'form' => $form->createView(),
//    ]);
//}
//
///**
// * @Route("/update/{id}/", name="update")
// * @param Test $test
// * @param Request $request
// * @return \Symfony\Component\HttpFoundation\Response
// */
//public function update(Test $test, Request $request)
//{
//    $form = $this->createForm(TestType::class, $test);
//    $form->handleRequest($request);
//    if ($form->isSubmitted() && $form->isValid()) {
//        $test = $form->getData();
//        $test = $this->testService->update($test);
//        $this->addFlash('success', 'Saved!');
//        return $this->redirect($this->generateUrl('tests.update', ['id' => $test->getId()]));
//    }
//    return $this->render('tests/form.html.twig', [
//        'form' => $form->createView(),
//    ]);
//}
