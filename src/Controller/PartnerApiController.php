<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PaymentType;
use App\Entity\Provider;
use App\Entity\Service;
use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
use App\Service\PublicTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Генерирует токен для доступа к услуге или токен на оплату услуги
 * Запрос должен идти скрытно.
 *
 * @Route("/partner/api", name="partner.api.", format="json")
 * https://testonomica.com/partner/api/token/
 */
class PartnerApiController extends AbstractRestController
{
    private PublicTokenService $publicTokenService;

    private ProviderRepository $providers;

    private ServiceRepository $services;

    const PAYMENT_TYPE_INTERNAL = 'internal';

    const PAYMENT_TYPE_EXTERNAL = 'external';

    const PAYMENT_TYPE_DEFAULT = self::PAYMENT_TYPE_INTERNAL;

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
     * Выпуск публичного токена
     * Будет возвращён либо токен оплаты, либо токен доступа.
     * Если передан payment_type=external, то будет создан доверительный платёж с последующим выпуском токена доступа.
     *
     * @Route("/token/", name="get_token")
     * @param Request $request
     * @return JsonResponse
     * @example /token/?token=PROVIDER_SECRET_TOKEN&user=PROVIDER_USER_ID&service=TESTONOMICA_SERVICE_NAME
     *  token - секретный токен партнёра
     *  user - id пользователя партнёра
     *  service - услуга
     *  payment_type (internal|external) - вид оплаты (на чьей стороне)
     */
    public function getToken(Request $request): JsonResponse
    {
        $service = $this->service($request);
        $provider = $this->provider($request);
        $user = self::user($request);
        $paymentType = self::paymentType($request);

        $tokenObject = $this->publicTokenService->token(
            $service,
            $provider,
            $user,
            $paymentType,
            self::isTestMode($request));

        return $this->json([
            'token' => $tokenObject->getToken()
        ]);
    }

    //
    // private methods
    //

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

    private static function paymentType(Request $request): PaymentType
    {
        $val = $request->get('payment_type', self::PAYMENT_TYPE_DEFAULT);
        if ($val === self::PAYMENT_TYPE_INTERNAL) {
            return new PaymentType(PaymentType::INTERNAL);
        } elseif ($val === self::PAYMENT_TYPE_EXTERNAL) {
            return new PaymentType(PaymentType::EXTERNAL);
        }
        throw new PreconditionFailedHttpException("Unsupported payment type: $val.");
    }

    private function service(Request $request): Service
    {
        $slug = $request->get('service');
        self::guardService($slug);
        $service = $this->services->getOneBySlug($slug);
        if (!$service) {
            throw new PreconditionFailedHttpException("Service \"$slug\" not found.");
        }
        return $service;
    }

    private function provider(Request $request): Provider
    {
        $token = $request->get('token');
        self::guardProviderToken($token);
        $provider = $this->providers->getByToken($token);
        if (!$provider) {
            throw new PreconditionFailedHttpException("Provider not found.");
        }
        return $provider;
    }

    private static function user(Request $request): string
    {
        $id = $request->get('user');
        self::guardProviderUser($id);
        return $id;
    }
}