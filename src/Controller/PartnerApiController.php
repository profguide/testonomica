<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Controller;


use App\Entity\Provider;
use App\Entity\Service;
use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
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
    // todo убрать, когда тестометрика начнет слать по новому
    const SERVICE_SLUG_DEFAULT = 'service_1';

    /**@var ProviderPaymentService */
    private $paymentService;

    /**@var ProviderRepository */
    private $providerRepository;

    /**@var ServiceRepository */
    private $serviceRepository;

    public function __construct(
        ProviderPaymentService $providerPaymentService,
        ProviderRepository $providerRepository,
        ServiceRepository $serviceRepository)
    {
        $this->paymentService = $providerPaymentService;
        $this->providerRepository = $providerRepository;
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Возвращает токен оплаты или токен доступа.
     * @Route("/token/", name="get_token")
     * @param Request $request
     * @example /token/?token=12313&user=1&service=proforientation
     * @return JsonResponse
     */
    public function getToken(Request $request)
    {
        $providerToken = $request->get('token');
        $providerUser = $request->get('user');
        $serviceSlug = $request->get('service', self::SERVICE_SLUG_DEFAULT);
        $provider = $this->loadProviderByToken($providerToken);
        $service = $this->loadServiceBySlug($serviceSlug);
        if ($this->isFreeAccessAllowed($provider)) {
            $tokenableObject = $this->paymentService->createAccessToken($service);
        } else {
            $tokenableObject = $this->paymentService->getToken($service, $provider, $providerUser);
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

    private function loadServiceBySlug(string $slug): Service
    {
        if (($service = $this->serviceRepository->findOneBySlug($slug)) == null) {
            throw new NotFoundHttpException();
        }
        return $service;
    }

    // однажды проверка станет сложнее, возможно будут тонкие правила, такие как доступ к некоторым услугам Service
    private function isFreeAccessAllowed(Provider $provider)
    {
        return $provider->getSlug() == 'profguide';
    }
}