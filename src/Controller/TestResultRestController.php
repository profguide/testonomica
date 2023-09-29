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
use App\Test\ViewFormat;
use App\V2\Progress\RawAnswersToProgressConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
        TestRepository    $tests,
        TestSourceService $questions,
        ResultService     $resultService,
        CalculatorService $calculatorService,
        ResultRenderer    $resultRenderer)
    {
        $this->tests = $tests;
        $this->questions = $questions;
        $this->resultService = $resultService;
        $this->calculatorService = $calculatorService;
        $this->resultRenderer = $resultRenderer;
    }

    //
    // actions
    //

    /**
     * @Route("/save/{testId<\d+>}/")
     * @param int $testId
     * @param Request $request
     * @return Response
     */
    public function save(int $testId, Request $request): Response
    {
        $test = $this->getTest($testId);

        // old style legacy for widget v 2.0.4
        $json = json_decode($request->getContent(), true);
        $answers = !empty($json['progress']) ? $json['progress'] : $request->get('progress');

        $rawAnswersToProgressConverter = new RawAnswersToProgressConverter();
        $progress = $rawAnswersToProgressConverter->convert($answers);

        $this->questions->validateRawAnswers($test, $progress);
        $result = $this->resultService->create($test, $progress);

        return $this->json(['key' => $result->getUuid()]);
    }

    /**
     * @Route("/result/{testId<\d+>}/")
     * additional params:
     * - format: html/pdf/json (html by default).
     * @param Request $request
     * @return Response
     */
    public function result(Request $request): Response
    {
        $key = $this->getRequestParameter($request, 'key');
        $result = $this->resultService->findByUuid($key);
        $data = $this->calculatorService->calculate($result);
        $test = $result->getTest();

        $format = new ViewFormat($request->get('format', ViewFormat::HTML));
        return $this->resultRenderer->render($test, $data, $format);
    }

    //
    // private methods
    //

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