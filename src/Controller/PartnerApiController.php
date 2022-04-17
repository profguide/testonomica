<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PaymentType;
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
 * @Route("/partner/api", name="partner.api.")
 * https://testonomica.com/partner/api/token/
 */
class PartnerApiController extends AbstractController
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
        $providerToken = $request->get('token');
        self::guardProviderToken($providerToken);
        $provider = $this->providers->getByToken($providerToken);

        $providerUser = $request->get('user');
        self::guardProviderUser($providerUser);

        $serviceSlug = $request->get('service');
        self::guardService($serviceSlug);
        $service = $this->services->getOneBySlug($serviceSlug);

        $paymentType = self::paymentType($request);

        $tokenObject = $this->publicTokenService->token($service, $provider, $providerUser, $paymentType, self::isTestMode($request));

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
}