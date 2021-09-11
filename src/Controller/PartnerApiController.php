<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Provider;
use App\Entity\Service;
use App\Payment\TokenableInterface;
use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
use App\Service\ProviderPaymentService;
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
    private ProviderPaymentService $paymentService;

    private ProviderRepository $providers;

    private ServiceRepository $services;

    public function __construct(
        ProviderPaymentService $providerPaymentService,
        ProviderRepository $providerRepository,
        ServiceRepository $serviceRepository)
    {
        $this->paymentService = $providerPaymentService;
        $this->providers = $providerRepository;
        $this->services = $serviceRepository;
    }

    /**
     * Возвращает токен оплаты или токен доступа.
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
        return $this->json([
            'token' => $this->generateSecretToken($service, $provider, $providerUser, $testMode)->getToken()
        ]);
    }

    /**
     * Генерирует секретный токен (токен доступа к услуге или токен оплаты) в зависимости от факта наличия оплаты.
     * @param Service $service
     * @param Provider $provider
     * @param $providerUser
     * @param bool $testMode
     * @return TokenableInterface
     */
    private function generateSecretToken(Service $service, Provider $provider, $providerUser, bool $testMode): TokenableInterface
    {
        if ($this->isFreeAccessAllowed($provider)) {
            // immediately give the free access to the user
            return $this->paymentService->generateAccessToken($service);
        } else {
            return $this->paymentService->generateToken($service, $provider, $providerUser, $testMode);
        }
    }

    /**
     * Провайдер с бесплатным уровнем доступа (профгид)
     * однажды проверка станет сложнее, возможно будут тонкие правила, такие как доступ к некоторым услугам Service
     * @param Provider $provider
     * @return bool
     */
    private function isFreeAccessAllowed(Provider $provider): bool
    {
        return $provider->getSlug() == 'profguide';
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