<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Test;
use App\Repository\TestRepository;
use App\Service\CalculatorService;
use App\Service\ResultService;
use App\Service\TestSourceService;
use App\Test\ResultRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/tests/api/v1")
 */
class TestResultRestController extends AbstractRestController implements AccessTokenAuthenticatedController
{
    private TestRepository $tests;

    private TestSourceService $questions;

    private ResultService $resultService;

    private CalculatorService $calculatorService;

    private ResultRenderer $resultRenderer;

    public function __construct(
        TestRepository $tests,
        TestSourceService $questions,
        ResultService $resultService,
        CalculatorService $calculatorService,
        ResultRenderer $resultRenderer)
    {
        $this->tests = $tests;
        $this->questions = $questions;
        $this->resultService = $resultService;
        $this->calculatorService = $calculatorService;
        $this->resultRenderer = $resultRenderer;
    }

    /**
     * @Route("/save/{testId<\d+>}/")
     * @param int $testId
     * @param Request $request
     * @return Response
     */
    public function save(int $testId, Request $request): Response
    {
        $test = $this->getTest($testId);
        $progress = $this->getRequestJsonParameter($request, 'progress');
        $answers = [];
        foreach ($progress as $qId => $values) {
            $answers[$qId] = self::createAnswer($qId, $values);
        }
        $this->questions->validateRawAnswers($test, $answers);
        $result = $this->resultService->create($test, $answers);

        return $this->json(['key' => $result->getUuid()]);
    }

    /**
     * @Route("/result/{testId<\d+>}/")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function result(Request $request): Response
    {
        $key = $this->getRequestParameter($request, 'key');
        $result = $this->resultService->findByUuid($key);
        $data = $this->calculatorService->calculate($result);
        $test = $result->getTest();
        return $this->resultRenderer->render($test, $data);
    }

    private function getRequestParameter(Request $request, string $name, bool $isRequired = true)
    {
        $value = $request->get($name);
        if ($value == null && $isRequired) {
            throw new \InvalidArgumentException("Parameter $name is required.");
        }
        return $value;
    }

    private function getRequestJsonParameter(Request $request, string $name)
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data[$name])) {
            throw new \InvalidArgumentException("Parameter $name is required.");
        }
        return $data[$name];
    }

    private function getTest(int $id): Test
    {
        $test = $this->tests->findOneById($id);
        if (!$test) {
            throw new NotFoundHttpException();
        }
        return $test;
    }

    private static function createAnswer(int $qId, $values): Answer
    {
        return new Answer((string)$qId, is_array($values) ? $values : [$values]);
    }
}