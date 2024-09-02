<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3;

use App\Controller\AbstractRestController;
use App\Entity\Result;
use App\Exception\ResultNotFoundException;
use App\Repository\ResultRepository;
use App\Service\AnswersExplainService;
use App\Service\CalculatorService;
use App\Test\Result\ResultKeyFactory;
use App\Test\ResultRenderer;
use App\Test\ViewFormat;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ResultController extends AbstractRestController
{
    const REQUEST_PARAM_RESULT_KEY = 'result_key';
    const REQUEST_PARAM_FORMAT = 'format';

    public function __construct(
        private readonly ResultRepository      $resultRepository,
        private readonly ResultKeyFactory      $resultKeyFactory,
        private readonly CalculatorService     $calculatorService,
        private readonly AnswersExplainService $answersExplainService,
        private readonly ResultRenderer        $resultRenderer)
    {
    }

    /**
     * todo test
     * Здесь происходит поиск результата, калькуляция и возврат его в указанном формате.
     *
     * Клиентский сайт передаёт постоянный RESULT_KEY.
     */
    #[Route('/partner/api/v3/result/get')]
    public function get(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $result = $this->getResultFromRequest($request);
            $format = $this->getFormatFromRequest($request);
            $calculatedData = $this->calculatorService->calculate($result);
            return $this->resultRenderer->render($result->getTest(), $calculatedData, $format);
        } catch (BadRequestException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 400);
        } catch (ResultNotFoundException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 404);
        } catch (\Exception $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 500);
        }
    }

    /**
     * Вопросы и ответы результата.
     * Используется службой контроля качества тестирования.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/partner/api/v3/answers/get')]
    public function answers(Request $request): Response
    {
        $result = $this->getResultFromRequest($request);
        return $this->json($this->answersExplainService->rows($result));
    }

    private function getResultFromRequest(Request $request): Result
    {
        $key = $request->get(self::REQUEST_PARAM_RESULT_KEY) ??
            throw new BadRequestException('The required "' . self::REQUEST_PARAM_RESULT_KEY . '" parameter is missing.');;

        $resultKey = $this->resultKeyFactory->create($key);
        return $this->resultRepository->findByKey($resultKey) ??
            throw $this->createNotFoundException('Result not found with provided key.');
    }

    private function getFormatFromRequest(Request $request): ViewFormat
    {
        $format = $request->get(self::REQUEST_PARAM_FORMAT);

        if (!$format) {
            throw new BadRequestException('The required "' . self::REQUEST_PARAM_FORMAT . '" parameter is missing.');
        }

        if (!in_array($format, [ViewFormat::JSON, ViewFormat::PDF])) {
            throw new BadRequestException("Unsupported \"" . self::REQUEST_PARAM_FORMAT . "\" value: \"$format\".");
        }

        return new ViewFormat($format);
    }
}