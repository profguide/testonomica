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
use App\Service\ProviderPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
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

    private ProviderPaymentService $providerPaymentService;

    private AccessService $accessService;

    private Robokassa $robokassa;

    private KernelInterface $kernel;

    public function __construct(
        PaymentService $paymentService,
        ProviderPaymentService $providerPaymentService,
        AccessService $accessService,
        Robokassa $robokassa,
        KernelInterface $kernel)
    {
        $this->paymentService = $paymentService;
        $this->providerPaymentService = $providerPaymentService;
        $this->accessService = $accessService;
        $this->robokassa = $robokassa;
        $this->kernel = $kernel;
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
        if (($providerPayment = $this->providerPaymentService->findOneByToken($token)) != null) {
            return $this->goToPayment($providerPayment, $this->isTestMode($request));
        } elseif (($access = $this->accessService->findOneByToken($token)) != null) {
            return $this->goToService($access, $token, $request);
        }
        throw new AccessDeniedHttpException('Unknown token.');
    }

    private function goToPayment(ProviderPayment $providerPayment, bool $isTestMode): RedirectResponse
    {
        $payment = $providerPayment->getPayment();
        if ($payment->isExecuted()) {
            // после оплаты токеном воспользоваться нельзя
            throw new AccessDeniedHttpException('The token has already been used.');
        }
        $response = new RedirectResponse($this->robokassa->createUrl($payment, $isTestMode));
        $this->paymentService->saveToCookie($payment, $response);
        return $response;
    }

    private function goToService(Access $access, string $token, Request $request): RedirectResponse
    {
        $cookieToken = $request->cookies->get('access');
        if ($cookieToken == $token || !$access->isUsed()) {
            // todo get test from service_test table
            $response = new RedirectResponse($this->generateUrl('tests.view', [
                'categorySlug' => 'business',
                'slug' => 'proforientation-v2'
            ]));
            $this->accessService->setCookie($access, $response);
            return $response;
        }
        throw new AccessDeniedHttpException('The token has already been used.');
    }

    private function isTestMode(Request $request): bool
    {
        return $request->get('isTest') == 1 || $this->kernel->isDebug();
    }
}