<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
use App\Service\PublicTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Генерирует токен для доступа к услуге или токен на оплату услуги
 * Запрос должен идти скрытно.
 *
 * https://testonomica.com/partner/api/token/
 * @Route("/partner/api", name="partner.api.")
 */
class PartnerApiController extends AbstractController
{
    private PublicTokenService $publicTokenService;

    private ProviderRepository $providers;

    private ServiceRepository $services;

    public function __construct(
        PublicTokenService $providerPaymentService,
        ProviderRepository $providerRepository,
        ServiceRepository $serviceRepository)
    {
        $this->publicTokenService = $providerPaymentService;
        $this->providers = $providerRepository;
        $this->services = $serviceRepository;
    }

    /**
     * Получение публичного токена.
     * Это будет либо токен оплаты, либо токен доступа.
     *
     * @Route("/token/", name="get_token")
     * @param Request $request
     * @return JsonResponse
     * @example /token/?token=12313&user=1&service=proforientation
     */
    public function getToken(Request $request): JsonResponse
    {
        $providerToken = $request->get('token');
        self::guardProviderToken($providerToken);
        $provider = $this->providers->getByToken($providerToken);

        $providerUser = $request->get('user');
        self::guardProviderUser($providerUser);

        $serviceSlug = $request->get('service');
        self::guardService($serviceSlug);
        $service = $this->services->getOneBySlug($serviceSlug);

        $testMode = self::isTestMode($request);

        $tokenObject = $this->publicTokenService->token($service, $provider, $providerUser, $testMode);

        return $this->json([
            'token' => $tokenObject->getToken()
        ]);
    }

    private static function guardProviderToken(?string $token)
    {
        if (empty($token)) {
            throw new PreconditionFailedHttpException("Token must be specified.");
        }
    }

    private static function guardProviderUser(?string $id)
    {
        if (empty($id)) {
            throw new PreconditionFailedHttpException("User must be specified.");
        }
    }

    private static function guardService(?string $serviceSlug)
    {
        if (empty($serviceSlug)) {
            throw new PreconditionFailedHttpException("Service must be specified.");
        }
    }

    private static function isTestMode(Request $request): bool
    {
        return $request->get('isTest', false) == 1;
    }
}