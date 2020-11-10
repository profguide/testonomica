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
 * @Route("/api/access", name="access.")
 * Class PartnerAccessController
 * @package App\Partner
 */
class PartnerAccessController extends AbstractController
{
    /**@var ProviderPaymentService */
    private $providerPaymentService;

    /**@var ProviderRepository */
    private $providerRepository;

    public function __construct(ProviderPaymentService $providerPaymentService, ProviderRepository $providerRepository)
    {
        $this->providerPaymentService = $providerPaymentService;
        $this->providerRepository = $providerRepository;

    }

    /**
     * @Route("/", name="provider")
     * @param Request $request
     * @return JsonResponse
     */
    public function providerAccess(Request $request)
    {
        $provider = $this->loadProviderByToken($request->get('token'));
        $tokenable = $this->providerPaymentService->paymentOrAccess($provider, $request->get('user'));
        return $this->json([
            'token' => $tokenable->getToken(),
        ]);
    }

    private function loadProviderByToken(string $id): Provider
    {
        if (($provider = $this->providerRepository->findByToken($id)) != null) {
            return $provider;
        }
        throw new NotFoundHttpException();
    }
}