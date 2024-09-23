<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3;

use App\Controller\AbstractRestController;
use App\Controller\Partner\Api\V3\Extractor\ProgressExtractor;
use App\Controller\Partner\Api\V3\Extractor\TestExtractor;
use App\Exception\ProgressValidationException;
use App\Exception\TestNotFoundException;
use App\Tests\Controller\Partner\Api\V3\ProgressControllerTest;
use App\V3\Result\Service\ProgressService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер для сохранения прогресса без ассоциации с пользователем.
 * @see ProgressControllerTest
 */
final class ProgressController extends AbstractRestController
{
    const REQUEST_PARAM_TEST = 'test';
    const REQUEST_PARAM_PROGRESS = 'progress';
    const RESPONSE_PARAM_RESULT_KEY = 'result_key';

    private const ERROR_CODE_MAP = [
        BadRequestException::class => [400],
        TestNotFoundException::class => [404],
        ProgressValidationException::class => [412, self::LOG_WARNING]
    ];

    public function __construct(protected readonly LoggerInterface $logger)
    {
    }

    /**
     * Сохраняет ответы без ассоциации с пользователем и возвращает ключ результата.
     * @see ResultAttachController
     *
     * Parameters:
     * - @link self::REQUEST_PARAM_TEST - slug теста
     * - @link self::REQUEST_PARAM_PROGRESS - список ответов
     */
    #[Route('/partner/api/v3/progress/save', methods: ['POST'])]
    public function save(
        Request           $request,
        TestExtractor     $testExtractor,
        ProgressExtractor $progressExtractor,
        ProgressService   $progressService): Response
    {
        try {
            $test = $testExtractor->extract($request, self::REQUEST_PARAM_TEST);
            $progress = $progressExtractor->extract($request, self::REQUEST_PARAM_PROGRESS);

            $result = $progressService->saveProgress($test, $progress);

            return $this->json([self::RESPONSE_PARAM_RESULT_KEY => $result->getNewId()]);
        } catch (\Throwable $e) {
            return $this->handleError($e, self::ERROR_CODE_MAP);
        }
    }
}