<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Payment;
use App\Entity\PaymentType;
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
        $providerPayment = $this->findOneByProviderAndUser($provider, $user);
        if ($providerPayment) {
            return $providerPayment->getPayment()->isExecuted();
        }

        return false;
    }

    public function save(ProviderPayment $providerPayment)
    {
        $this->providerPaymentRepository->save($providerPayment);
    }

    public function create(Provider $provider, string $user, Service $service, bool $testMode): ProviderPayment
    {
        $providerPayment = $this->findOneByProviderAndUser($provider, $user);
        if ($providerPayment) {
            return $providerPayment;
        }

        $payment = Payment::init($service, $service->getSum(), $testMode);
        $payment = $this->paymentService->save($payment);
        return $this->providerPaymentRepository->save(ProviderPayment::init($payment, $provider, $user, new PaymentType(PaymentType::DEFAULT)));
    }

    public function createTrust(Provider $provider, string $user, Service $service): ProviderPayment
    {
        // just in case to avoid doubling
        $providerPayment = $this->findOneByProviderAndUser($provider, $user);
        if ($providerPayment) {
            return $providerPayment;
        }

        $payment = Payment::init($service, $service->getSum());
        $payment->addStatusExecuted();
        $payment = $this->paymentService->save($payment);
        return $this->providerPaymentRepository->save(ProviderPayment::init($payment, $provider, $user, new PaymentType(PaymentType::EXTERNAL)));
    }

    private function findOneByProviderAndUser(Provider $provider, string $user): ?ProviderPayment
    {
        return $this->providerPaymentRepository->findOneByProviderAndUser($provider, $user);
    }
}