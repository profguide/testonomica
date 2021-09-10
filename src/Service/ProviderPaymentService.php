<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Service;


use App\Entity\Access;
use App\Entity\Payment;
use App\Entity\Provider;
use App\Entity\ProviderPayment;
use App\Entity\Service;
use App\Payment\TokenableInterface;
use App\Repository\ProviderPaymentRepository;

class ProviderPaymentService
{
    private PaymentService $paymentService;

    private AccessService $accessService;

    private ProviderPaymentRepository $providerPaymentRepository;

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
     * Генерирует токен пользователя на оплату или на доступ к услуге.
     * Определяется фактом наличия оплаты услуги для указанного пользователя из системы поставщика.
     * @param Service $service
     * @param Provider $provider
     * @param string $user
     * @param bool $testMode
     * @return TokenableInterface
     */
    public function generateToken(Service $service, Provider $provider, string $user, bool $testMode = false): TokenableInterface
    {
        if ($this->isPayed($provider, $user, $service)) {
            return $this->generateAccessToken($service);
        } else {
            return $this->generatePaymentToken($service, $provider, $user, $testMode);
        }
    }

    /**
     * Была ли куплена услуга пользователем из системы поставщика (пока любая услуга для простоты).
     * @param Provider $provider
     * @param string $user
     * @param Service $service
     * @return bool
     */
    private function isPayed(Provider $provider, string $user, Service $service): bool
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
     * @param bool $testMode
     * @return ProviderPayment
     */
    private function generatePaymentToken(Service $service, Provider $provider, string $user, bool $testMode): ProviderPayment
    {
        if (($providerPayment = $this->findOneByProviderAndUser($provider, $user)) != null) {
            return $providerPayment;
        }
        return $this->create($service, $provider, $user, $testMode);
    }

    /**
     * @param Service $service
     * @return Access
     */
    public function generateAccessToken(Service $service): Access
    {
        return $this->accessService->create($service);
    }

    private function create(Service $service, Provider $provider, string $user, bool $testMode): ProviderPayment
    {
        $payment = $this->createPaymentForServiceTest($service, $testMode);
        $providerPayment = ProviderPayment::init($payment, $provider, $user);
        return $this->providerPaymentRepository->save($providerPayment);
    }

    //
    private function createPaymentForServiceTest(Service $service, bool $testMode): Payment
    {
        return $this->paymentService->create($service, $service->getSum(), $testMode);
    }

    public function findOneByToken(string $token): ?ProviderPayment
    {
        return $this->providerPaymentRepository->findByToken($token);
    }
}