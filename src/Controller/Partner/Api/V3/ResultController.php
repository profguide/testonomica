<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3;

use App\Controller\AbstractRestController;
use App\Controller\Partner\Api\V3\Extractor\ResultExtractor;
use App\Controller\Partner\Api\V3\Extractor\ViewFormatExtractor;
use App\Exception\ResultNotFoundException;
use App\Service\AnswersExplainService;
use App\V3\Result\Service\ResultService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Здесь буквально нечего тестировать.
 */
final class ResultController extends AbstractRestController
{
    const REQUEST_PARAM_RESULT_KEY = 'result_key';
    const REQUEST_PARAM_FORMAT = 'format';

    private const ERROR_MAP = [
        BadRequestException::class => [400],
        ResultNotFoundException::class => [404]
    ];

    public function __construct(protected readonly LoggerInterface $logger)
    {
    }

    /**
     * Возвращает результат калькуляции в указанном формате
     *
     * @param Request $request
     * @param ResultExtractor $resultExtractor
     * @param ViewFormatExtractor $formatExtractor
     * @param ResultService $resultService
     * @return Response
     */
    #[Route('/partner/api/v3/result/get')]
    public function get(
        Request             $request,
        ResultExtractor     $resultExtractor,
        ViewFormatExtractor $formatExtractor,
        ResultService       $resultService,
    ): Response
    {
        try {
            $result = $resultExtractor->extract($request, self::REQUEST_PARAM_RESULT_KEY);
            $format = $formatExtractor->extract($request, self::REQUEST_PARAM_FORMAT);

            return $resultService->getResultResponse($result, $format);
        } catch (\Throwable $e) {
            return $this->handleError($e, self::ERROR_MAP);
        }
    }

    /**
     * Возвращает вопросы и ответы к результату.
     * Используется службой контроля качества тестирования.
     *
     * @param Request $request
     * @param ResultExtractor $resultExtractor
     * @param AnswersExplainService $answersExplainService
     * @return Response
     */
    #[Route('/partner/api/v3/answers/get')]
    public function answers(
        Request               $request,
        ResultExtractor       $resultExtractor,
        AnswersExplainService $answersExplainService
    ): Response
    {
        $result = $resultExtractor->extract($request, self::REQUEST_PARAM_RESULT_KEY);

        return $this->json($answersExplainService->rows($result));
    }
}