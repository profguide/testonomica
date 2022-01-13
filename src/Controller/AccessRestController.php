<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Access;
use App\Entity\ProviderPayment;
use App\Service\AccessService;
use App\Service\ProviderUserPaymentService;
use App\Service\PublicTokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/access", name="access.")
 */
class AccessRestController extends RestController
{
    private PublicTokenService $publicTokenService;

    private ProviderUserPaymentService $providerPaymentService;

    private AccessService $accessService;

    public function __construct(
        AccessService $accessService,
        PublicTokenService $publicTokenService,
        ProviderUserPaymentService $providerPaymentService)
    {
        $this->publicTokenService = $publicTokenService;
        $this->providerPaymentService = $providerPaymentService;
        $this->accessService = $accessService;
    }

    /**
     * Checks if token is a valid Access (that is if person has a permission).
     * @Route ("/has/", name="has")
     * @param Request $request
     * @return Response
     */
    public function hasAccess(Request $request): Response
    {
        $token = $request->headers->get('token');
        if (!$token) {
            $token = $this->accessService->getCookie($request);
            if (!$token) {
                return $this->json(['status' => false]);
            }
        }

        $tokenObject = $this->publicTokenService->find($token);
        if (!$tokenObject) {
            return $this->json(['status' => false]);
        }

        if ($tokenObject instanceof ProviderPayment) {
            /**@var ProviderPayment $payment */
            $payment = $tokenObject;
            if ($payment->getPayment()->isExecuted()) {
                return $this->json(['status' => true]); // todo think of returning false
            }
        } elseif ($tokenObject instanceof Access) {
            $access = $tokenObject;
            if (!$access->isUsed()) {
                return $this->json(['status' => true]);
            }
        }

        return $this->json(['status' => false]);
    }

    /**
     * Находит и возвращает заказ по токену оплаты (заказа)
     * @Route ("/order/", name="order")
     * @param Request $request
     * @return Response
     */
    public function order(Request $request): Response
    {
        $token = $this->getTokenRequest($request);
        $providerPayment = $this->getProviderPayment($token);
        $payment = $providerPayment->getPayment();
        if ($payment->isExecuted()) {
            throw new AccessDeniedHttpException('Order has been already executed.');
        }

        return $this->json([
            'order' => [
                'id' => $payment->getId(),
                'description' => 'Оплата теста',
                'price' => $payment->getSum(),
                'count' => 1,
                'sum' => $payment->getSum(),
            ]
        ]);
    }

    /**
     * Выдаёт первый Access после выполненного платежа.
     * Срабатывает только 1 раз после платежа.
     * Последующие вызовы порождают исключение.
     * @Route ("/grand/", name="change")
     * @param Request $request
     * @return Response
     */
    public function grand(Request $request): Response
    {
        $token = $this->getTokenRequest($request);
        $providerPayment = $this->getProviderPayment($token);
        $payment = $providerPayment->getPayment();
        if (!$payment->isExecuted()) {
            throw new AccessDeniedHttpException('Order has not been executed yet.');
        }
        $access = $this->publicTokenService->createFirstAccessAfterPayment($providerPayment);
        return $this->json(['token' => $access->getToken()]);
    }

    private function getTokenRequest(Request $request): string
    {
        $token = $request->headers->get('token');
        if (empty($token)) {
            $token = $this->accessService->getCookie($request);
            if ($token) {
                return $token;
            }
        }
        throw new AccessDeniedHttpException('No token specified.');
    }

    private function getProviderPayment(string $token): ProviderPayment
    {
        $providerPayment = $this->providerPaymentService->findOneByToken($token);
        if (!$providerPayment) {
            throw new AccessDeniedHttpException('Order not found.');
        }
        return $providerPayment;
    }
}