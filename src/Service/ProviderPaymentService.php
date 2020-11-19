<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Service;


use App\Entity\Provider;
use App\Entity\Access;
use App\Entity\ProviderPayment;
use App\Entity\Service;
use App\Payment\TokenableInterface;
use App\Repository\ProviderPaymentRepository;

class ProviderPaymentService
{
    /**@var PaymentService */
    private $paymentService;

    /**@var AccessService */
    private $accessService;

    /**@var ProviderPaymentRepository */
    private $providerPaymentRepository;

    /**
     * @param PaymentService $paymentService
     * @param AccessService $accessService
     * @param ProviderPaymentRepository $providerPaymentRepository
     */
    public function __construct(
        PaymentService $paymentService,
        AccessService $accessService,
        ProviderPaymentRepository $providerPaymentRepository)
    {
        $this->paymentService = $paymentService;
        $this->accessService = $accessService;
        $this->providerPaymentRepository = $providerPaymentRepository;
    }

    public function findOneByProviderAndUser(Provider $provider, string $user): ?ProviderPayment
    {
        return $this->providerPaymentRepository->findOneByProviderAndUser($provider, $user);
    }

    /**
     * @param Service $service
     * @param Provider $provider
     * @param string $user
     * @return TokenableInterface
     */
    public function getToken(Service $service, Provider $provider, string $user): TokenableInterface
    {
        if ($this->isPayed($provider, $user)) {
            return $this->createAccessToken($service);
        } else {
            return $this->getPaymentToken($service, $provider, $user);
        }
    }

    private function isPayed(Provider $provider, string $user)
    {
        if (($providerPayment = $this->findOneByProviderAndUser($provider, $user)) == null) {
            return false;
        }
        return $providerPayment->getPayment()->isExecuted();
    }

    /**
     * @param Service $service
     * @param Provider $provider
     * @param string $user
     * @return ProviderPayment
     */
    private function getPaymentToken(Service $service, Provider $provider, string $user): ProviderPayment
    {
        if (($providerPayment = $this->findOneByProviderAndUser($provider, $user)) != null) {
            return $providerPayment;
        }
        return $this->create($service, $provider, $user);
    }

    /**
     * @param Service $service
     * @return Access
     */
    public function createAccessToken(Service $service): Access
    {
        return $this->accessService->create($service);
    }

    private function create(Service $service, Provider $provider, string $user): ProviderPayment
    {
        $payment = $this->createPaymentForServiceTest($service);
        $providerPayment = ProviderPayment::init($payment, $provider, $user);
        return $this->providerPaymentRepository->save($providerPayment);
    }

    //
    private function createPaymentForServiceTest(Service $service)
    {
        return $this->paymentService->create($service, $service->getSum());
    }

    public function findOneByToken(string $token): ?ProviderPayment
    {
        return $this->providerPaymentRepository->findByToken($token);
    }
}