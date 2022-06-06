<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Controller;


use App\Entity\Access;
use App\Entity\ProviderPayment;
use App\Payment\Robokassa;
use App\Service\AccessService;
use App\Service\PaymentService;
use App\Service\ProviderUserPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Перенаправляет на оплату или на услугу, если она уже была оплачена.
 *
 * https://api.testonomica.com/access/provide --> https://testonomica.com/partner/provide/
 * @Route("/partner", name="partner.")
 */
class PartnerProvideController extends AbstractController
{
    private PaymentService $paymentService;

    private ProviderUserPaymentService $providerUserPaymentService;

    private AccessService $accessService;

    private Robokassa $robokassa;

    public function __construct(
        PaymentService $paymentService,
        ProviderUserPaymentService $providerUserPaymentService,
        AccessService $accessService,
        Robokassa $robokassa)
    {
        $this->paymentService = $paymentService;
        $this->providerUserPaymentService = $providerUserPaymentService;
        $this->accessService = $accessService;
        $this->robokassa = $robokassa;
    }

    /**
     * Это место служит телепортом в услугу или в оплату услуги
     * @Route("/provide/", name="provide")
     * @param Request $request
     * @return RedirectResponse
     */
    public function provide(Request $request): RedirectResponse
    {
        $token = $request->get('token');
        self::guardToken($token);
        if (($providerPayment = $this->providerUserPaymentService->findOneByToken($token)) != null) {
            return $this->goToPayment($providerPayment);
        } elseif (($access = $this->accessService->findOneByToken($token)) != null) {
            return $this->goToService($request, $access, $token);
        }
        throw new AccessDeniedHttpException('Unknown token.');
    }

    private function goToPayment(ProviderPayment $providerPayment): RedirectResponse
    {
        $payment = $providerPayment->getPayment();
        if ($payment->isExecuted()) {
            // после оплаты токеном воспользоваться нельзя
            throw new AccessDeniedHttpException('The token has already been used.');
        }
        return new RedirectResponse($this->robokassa->createUrl($payment));
    }

    private function goToService(Request $request, Access $access, string $token): RedirectResponse
    {
        $cookieToken = $request->cookies->get('access');
        if ($cookieToken == $token || !$access->isUsed()) {
            // todo get test from service_test table
            $response = new RedirectResponse($this->generateUrl('tests.view', [
                'slug' => 'proforientation-v2'
            ]));
            $this->accessService->setCookie($access, $response);
//            $response->send(); // todo это точно надо? во время тестов оно выводит в консоль html
            return $response;
        }
        throw new AccessDeniedHttpException('The token has already been used.');
    }

    private static function guardToken($token)
    {
        if (empty($token)) {
            throw new PreconditionFailedHttpException("Token must be specified.");
        }
    }
}