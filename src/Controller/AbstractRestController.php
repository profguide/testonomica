<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRestController extends AbstractController
{
    protected const LOG_EXCEPTION = 'exception';
    protected const LOG_WARNING = 'warning';
    protected const THROW_PREVIOUS = 'previous';

    protected readonly LoggerInterface $logger;

    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $response = parent::json($data, $status, $headers, $context);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $response;
    }

    protected function createErrorResponse(string $message, int $statusCode): Response
    {
        return $this->json(['error' => ['message' => $message]], $statusCode);
    }

    /**
     * Handles exception according to rules
     * Logs warning or exception if log level was specified
     * Exception that wrap other exceptions might be marked as self::THROW_PREVIOUS
     *
     * ```php
     * return $this->handleError($e, [
     *      BadRequestException::class => [400],
     *      UserNotFoundException::class => [404],
     *      PaymentPolicyValidationException::class => [412, 'warning'], << that will be logged as warning
     *      PaymentPolicyValidationException::class => [412, 'exception'], << that will be logged as exception with trace
     *      HandlerFailedException::class => self::THROW_PREVIOUS << that will be fallen to previous exception
     * ]);
     * ```
     * The rest exceptions are interpreted as internal
     *
     * @param \Throwable $e
     * @param array $codeMap
     * @return Response
     */
    protected function handleError(\Throwable $e, array $codeMap): Response
    {
        // Handle 'previous' exception case
        $previousException = $e->getPrevious();
        if ($previousException && ($codeMap[get_class($e)] ?? null) === self::THROW_PREVIOUS) {
            return $this->handleError($previousException, $codeMap); // recursion
        }

        foreach ($codeMap as $class => $params) {
            if ($e instanceof $class) {
                $code = $params[0];
                $logLevel = $params[1] ?? null;

                // Log the exception if log level is specified
                if ($logLevel) {
                    $this->logException($e, $logLevel);
                }

                return $this->createErrorResponse($e->getMessage(), $code);
            }
        }

        // Fallback for unexpected exceptions
        $this->logException($e, self::LOG_EXCEPTION);
        return $this->createErrorResponse($e->getMessage(), 500);
    }

    private function logException(\Throwable $e, string $logLevel): void
    {
        if ($logLevel === self::LOG_WARNING) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
        } else {
            $this->logger->error($e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);
        }
    }
}