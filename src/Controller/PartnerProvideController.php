<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Controller;


use App\Payment\Robokassa;
use App\Service\AccessService;
use App\Service\PaymentService;
use App\Service\ProviderPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * https://api.testonomica.com/access/provide --> https://testonomica.com/partner/provide/
 * @Route("/partner", name="partner.")
 */
class PartnerProvideController extends AbstractController
{
    /**@var PaymentService */
    private $paymentService;

    /**@var ProviderPaymentService */
    private $providerPaymentService;

    /**@var AccessService */
    private $accessService;

    /**@var Robokassa */
    private $robokassa;

    /**@var KernelInterface */
    private $kernel;

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
    public function provide(Request $request)
    {
        $token = $request->get('token');
        // payment
        if (($providerPayment = $this->providerPaymentService->findOneByToken($token)) != null) {
            $payment = $providerPayment->getPayment();
            if (!$payment->isExecuted()) {
                $response = new RedirectResponse($this->robokassa->createUrl($payment, $this->kernel->isDebug()));
                $this->paymentService->saveToCookie($payment, $response);
                return $response;
            } else {
                // после оплаты токеном воспользоваться нельзя (защита от поисковиков)
                throw new AccessDeniedHttpException('The token has already been used.');
            }
        }
        // access
        if (($providerAccess = $this->accessService->findOneByToken($token)) != null) {
            $cookie = $request->cookies->get('access');
            if ($cookie == $token || !$providerAccess->isUsed()) {
                $response = new RedirectResponse($this->generateUrl('tests.view', [
                    'categorySlug' => 'psychology',
                    'slug' => 'test_2'
                ]));
                $this->accessService->saveToCookie($providerAccess, $response);
                return $response;
            } else {
                throw new AccessDeniedHttpException('The token has already been used.');
            }
        }
        throw new AccessDeniedHttpException('Token not found.');
    }
}