<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Result;
use App\Entity\Test;
use App\Repository\ResultRepository;
use App\Repository\TestRepository;
use App\Service\CalculatorService;
use App\Test\Result\ResultKeyFactory;
use App\Test\ResultRenderer;
use App\Test\ViewFormat;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/partner/api/test", name="partner.api.test.")
 */
class PartnerApiTestController extends AbstractRestController
{
    private TestRepository $tests;

    private ResultRepository $results;

    private ResultRenderer $renderer;

    public function __construct(TestRepository $tests, ResultRepository $results, ResultRenderer $renderer, private readonly ResultKeyFactory $resultKeyFactory)
    {
        $this->tests = $tests;
        $this->results = $results;
        $this->renderer = $renderer;
    }

    /**
     * @Route("/result/{key}/", name="result")
     * additional params:
     * - format: html/pdf/json (json by default).
     * @param string $key
     * @param Request $request
     * @param CalculatorService $calculatorService
     * @return JsonResponse
     */
    public function result(string $key, Request $request, CalculatorService $calculatorService): Response
    {
        $result = $this->getResult($key);
        $data = $calculatorService->calculate($result);
        $format = new ViewFormat($request->get('format', ViewFormat::JSON));
        return $this->renderer->render($result->getTest(), $data, $format);
    }

    /**
     * @Route("/progress/{uuid}/", name="progress")
     *
     * Прогресс теста
     * @param string $uuid
     * @return Response
     */
    public function actionProgress(string $uuid): Response
    {
        $result = $this->getResult($uuid);
        return $this->json([
            'progress' => $result->getData()
        ]);
    }

    /**
     * @Route("/calculate/{id}/", name="calculate")
     *
     * Расчёт результата
     *
     * @param string $id
     * @param Request $request
     * @param CalculatorService $calculatorService
     * @return Response
     */
    public function calculate(string $id, Request $request, CalculatorService $calculatorService): Response
    {
        $format = new ViewFormat($request->get('format', ViewFormat::JSON));

        $result = new Result();
        $result->setData($request->get('progress'));
        $result->setTest($this->getTest($id));

        $data = $calculatorService->calculate($result);

        if ($format->is(ViewFormat::JSON)) {
            return $this->json($data);
        } else {
            return $this->renderer->render($result->getTest(), $data, $format);
        }
    }

    protected function getTest(int|string $id): Test
    {
        if (is_numeric($id)) {
            $test = $this->tests->findOneById((int)$id);
        } else {
            $test = $this->tests->findOneBySlug($id);
        }
        if (!$test) {
            throw self::createNotFoundException();
        }
        return $test;
    }

    private function getResult(string $key): Result
    {
        $resultKey = $this->resultKeyFactory->create($key);

        $result = $this->results->findByKey($resultKey);
        if (!$result) {
            throw self::createNotFoundException();
        }

        return $result;
    }
}