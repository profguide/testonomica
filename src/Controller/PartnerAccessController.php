<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Controller;


use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Service\AccessService;
use App\Service\ProviderPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * https://testonomica.com/api/access/
 * https://testonomica.com/api/access/provide/
 * @Route("/api/access", name="access.")
 * Class PartnerAccessController
 * @package App\Partner
 */
class PartnerAccessController extends AbstractController
{
    /**@var ProviderPaymentService */
    private $paymentService;

    /**@var AccessService */
    private $accessService;

    /**@var ProviderRepository */
    private $providerRepository;

    public function __construct(
        ProviderPaymentService $providerPaymentService,
        AccessService $accessService,
        ProviderRepository $providerRepository)
    {
        $this->paymentService = $providerPaymentService;
        $this->accessService = $accessService;
        $this->providerRepository = $providerRepository;
    }

    /**
     * Возвращает токен оплаты или токен доступа.
     * @Route("/", name="get-token")
     * @param Request $request
     * @return JsonResponse
     */
    public function getToken(Request $request)
    {
        $providerToken = $request->get('token');
        $providerUser = $request->get('user');
        $provider = $this->loadProviderByToken($providerToken);
        if ($this->isFreeAccessAllowed($provider)) {
            $tokenableObject = $this->paymentService->createAccessToken($provider);
        } else {
            $tokenableObject = $this->paymentService->getToken($provider, $providerUser);
        }
        return $this->json([
            'token' => $tokenableObject->getToken(),
        ]);
    }

    /**
     * Подумать над названием.
     * Это место служит телепортом в услугу или в оплату услуги
     * Сейчас это https://api.testonomica.com/access/provide
     * А надо: https://testonomica.com/api/access/provide/
     * @Route("/provide/", name="provide-service")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function provideByToken(Request $request)
    {
        $token = $request->get('token');
        // payment
        if (($providerPayment = $this->paymentService->findOneByToken($token)) != null) {
            if (!$providerPayment->getPayment()->isExecuted()) {
                $response = new RedirectResponse('GO_TO_PAYMENT_SERVICE');
                $response->headers->setCookie(Cookie::create('payment', $token, 60 * 60 * 24 * 365));
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
                $response->headers->setCookie(Cookie::create('access', $token, 60 * 60 * 24 * 365));
                return $response;
            } else {
                throw new AccessDeniedHttpException('The token has already been used.');
            }
        }
        throw new AccessDeniedHttpException('Token not found.');
    }

    private function loadProviderByToken(string $id): Provider
    {
        if (($provider = $this->providerRepository->findByToken($id)) != null) {
            return $provider;
        }
        throw new NotFoundHttpException();
    }

    // однажды проверка станет сложнее, возможно будут тонкие правила, такие как доступ к некоторым тестам от одного партнера
    private function isFreeAccessAllowed(Provider $provider)
    {
        return $provider->getSlug() == 'profguide';
    }
}