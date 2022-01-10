<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Provider;
use App\Entity\ProviderPayment;
use App\Entity\Service;
use App\Repository\ProviderPaymentRepository;

class ProviderUserPaymentService
{
    private PaymentService $paymentService;

    private ProviderPaymentRepository $providerPaymentRepository;

    public function __construct(PaymentService $paymentService, ProviderPaymentRepository $providerPaymentRepository)
    {
        $this->paymentService = $paymentService;
        $this->providerPaymentRepository = $providerPaymentRepository;
    }

    public function findOneByToken(string $token): ?ProviderPayment
    {
        return $this->providerPaymentRepository->findByToken($token);
    }

    public function hasExecutedPayment(Provider $provider, string $user): bool
    {
        // Для профгида все сервисы бесплатные
        if ($provider->getSlug() == 'profguide') {
            return true;
        }
        $providerPayment = $this->findOneByProviderAndUser($provider, $user);
        if ($providerPayment) {
            return $providerPayment->getPayment()->isExecuted();
        }

        return false;
    }

    public function create(Provider $provider, string $user, Service $service, bool $testMode): ProviderPayment
    {
        $providerPayment = $this->findOneByProviderAndUser($provider, $user);
        if ($providerPayment) {
            return $providerPayment;
        }

        $payment = $this->paymentService->create($service, $testMode);

        return $this->providerPaymentRepository->save(ProviderPayment::init($payment, $provider, $user));
    }

    private function findOneByProviderAndUser(Provider $provider, string $user): ?ProviderPayment
    {
        return $this->providerPaymentRepository->findOneByProviderAndUser($provider, $user);
    }
}