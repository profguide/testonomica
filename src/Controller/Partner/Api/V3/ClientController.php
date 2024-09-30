<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3;

use App\Controller\AbstractRestController;
use App\Controller\Partner\Api\V3\Extractor\ProviderExtractor;
use App\Exception\ProviderNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер для аутентификации компании
 */
class ClientController extends AbstractRestController
{
    const REQUEST_PARAM_TOKEN = 'token';

    private const ERROR_CODE_MAP = [
        BadRequestException::class => [400],
        ProviderNotFoundException::class => [401, self::LOG_WARNING]
    ];

    public function __construct(protected readonly LoggerInterface $logger)
    {
    }

    /**
     * Аутентификация по токену.
     * Наверно это не самый правильный способ.
     * Но не вижу пока причин использовать логин и пароль.
     *
     * @param Request $request
     * @param ProviderExtractor $providerExtractor
     * @return Response
     */
    #[Route('/partner/api/v3/client/authenticate-by-token', methods: ['POST'])]
    public function authenticateByToken(
        Request           $request,
        ProviderExtractor $providerExtractor): Response
    {
        try {
            $provider = $providerExtractor->extract($request, self::REQUEST_PARAM_TOKEN);
            return $this->json([
                'name' => $provider->getName(),
                'key' => $provider->getToken()
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e, self::ERROR_CODE_MAP);
        }
    }
}