<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Controller;


use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Service\ProviderPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * https://testonomica.com/partner/api/token/
 * @Route("/partner/api", name="partner.api.")
 */
class PartnerApiController extends AbstractController
{
    /**@var ProviderPaymentService */
    private $paymentService;

    /**@var ProviderRepository */
    private $providerRepository;

    public function __construct(
        ProviderPaymentService $providerPaymentService,
        ProviderRepository $providerRepository)
    {
        $this->paymentService = $providerPaymentService;
        $this->providerRepository = $providerRepository;
    }

    /**
     * Возвращает токен оплаты или токен доступа.
     * @Route("/token/", name="get_token")
     * @param Request $request
     * @return JsonResponse
     */
    public function getToken(Request $request)
    {
        $providerToken = $request->get('token');
        $providerUser = $request->get('user');
        $provider = $this->loadProviderByToken($providerToken);
        if ($this->isFreeAccessAllowed($provider)) {
            $tokenableObject = $this->paymentService->createAccessToken();
        } else {
            $tokenableObject = $this->paymentService->getToken($provider, $providerUser);
        }
        return $this->json([
            'token' => $tokenableObject->getToken(),
        ]);
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