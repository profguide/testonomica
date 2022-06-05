<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Result;
use App\Entity\Test;
use App\Repository\ResultRepository;
use App\Repository\TestRepository;
use App\Service\CalculatorService;
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

    public function __construct(TestRepository $tests, ResultRepository $results, ResultRenderer $renderer)
    {
        $this->tests = $tests;
        $this->results = $results;
        $this->renderer = $renderer;
    }

    /**
     * @Route("/result/{key}/", name="result")
     *
     * todo Студентика имеет доступ без оплаты. Надо срочно продумать.
     *
     * @param string $key
     * @param Request $request
     * @param CalculatorService $calculatorService
     * @return JsonResponse
     */
    public function result(string $key, Request $request, CalculatorService $calculatorService): Response
    {
        $format = new ViewFormat($request->get('format', ViewFormat::JSON));

        $result = $this->results->findByUuid($key);
        $data = $calculatorService->calculate($result);
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
        $result = $this->results->findByUuid($uuid);
        if (!$result) {
            throw self::createNotFoundException();
        }
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
        $result = new Result();
        $result->setData($request->get('progress'));
        $result->setTest($this->getTest((int)$id));

        return $this->json($calculatorService->calculate($result));
    }

    protected function getTest(int $id): Test
    {
        $test = $this->tests->findOneById($id);
        if (!$test) {
            throw self::createNotFoundException();
        }
        return $test;
    }
}