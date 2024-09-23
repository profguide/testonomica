<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3;

use App\Controller\AbstractRestController;
use App\Controller\Partner\Api\V3\Extractor\ProviderExtractor;
use App\Controller\Partner\Api\V3\Extractor\ResultExtractor;
use App\Controller\Partner\Api\V3\Extractor\UserIdExtractor;
use App\Exception\ProgressValidationException;
use App\Exception\ProviderNotFoundException;
use App\Exception\ResultNotFoundException;
use App\Tests\Controller\Partner\Api\V3\ResultAttachControllerTest;
use App\V2\Provider\Policy\Payment\Validator\Exception\PaymentPolicyValidationException;
use App\V3\Result\Exception\ResultAlreadyAttachedAnotherUserException;
use App\V3\Result\Service\ResultAttachmentService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
 * There is no reasons to test everything here except for output because
 * the almost everything is delegated
 * @see ResultAttachControllerTest
 */
final class ResultAttachController extends AbstractRestController
{
    private const REQUEST_PARAM_PROVIDER = 'client_key';
    private const REQUEST_PARAM_USER = 'user_id';
    private const REQUEST_PARAM_RESULT = 'result_key';
    private const RESPONSE_STATUS = 'status';

    protected const ERROR_CODE_MAP = [
        BadRequestException::class => [400],
        ResultNotFoundException::class => [404],
        UserNotFoundException::class => [404],
        ProviderNotFoundException::class => [404],
        ProgressValidationException::class => [412, self::LOG_WARNING],
        PaymentPolicyValidationException::class => [412, self::LOG_WARNING],
        ResultAlreadyAttachedAnotherUserException::class => [412, self::LOG_WARNING],
        HandlerFailedException::class => self::THROW_PREVIOUS
    ];

    public function __construct(protected readonly LoggerInterface $logger)
    {
    }

    /**
     * Ассоциирует результат с пользователем.
     *
     * - @link self::REQUEST_PARAM_PROVIDER
     * - @link self::REQUEST_PARAM_RESULT
     * - @link self::REQUEST_PARAM_USER
     */
    #[Route('/partner/api/v3/result/attach', methods: ['POST'])]
    public function attach(
        Request                 $request,
        ProviderExtractor       $providerExtractor,
        UserIdExtractor         $userIdExtractor,
        ResultExtractor         $resultExtractor,
        ResultAttachmentService $attachmentService): Response
    {
        try {
            $provider = $providerExtractor->extract($request, self::REQUEST_PARAM_PROVIDER);
            $result = $resultExtractor->extract($request, self::REQUEST_PARAM_RESULT);
            $userId = $userIdExtractor->extract($request, self::REQUEST_PARAM_USER);

            $attachmentService->attachResult($provider, $result, $userId);

            return $this->json([self::RESPONSE_STATUS => true]);
        } catch (\Throwable $e) {
            return $this->handleError($e, self::ERROR_CODE_MAP);
        }
    }
}