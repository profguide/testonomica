<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Service;


use App\Entity\Provider;
use App\Entity\ProviderAccess;
use App\Entity\ProviderPayment;
use App\Payment\TokenableInterface;
use App\Repository\AccessRepository;
use App\Repository\ProviderPaymentRepository;

class ProviderPaymentService
{
    /**@var ProviderPaymentRepository */
    private $providerPaymentRepository;

    /**@var AccessRepository */
    private $accessRepository;

    /**
     * ProviderPaymentService constructor.
     * @param ProviderPaymentRepository $providerPaymentRepository
     * @param AccessRepository $accessRepository
     */
    public function __construct(ProviderPaymentRepository $providerPaymentRepository, AccessRepository $accessRepository)
    {
        $this->providerPaymentRepository = $providerPaymentRepository;
        $this->accessRepository = $accessRepository;
    }

    public function paymentOrAccess(Provider $provider, string $user): TokenableInterface
    {
        /*
         * Партнёр обращается в PartnerAccessController чтобы получить токен к тесту
         * 1. PartnerAccessController проверяет есть ли оплата.
         * Если есть оплата:
         *  вернем код доступа к тесту
         * Если нет оплаты:
         *  вернем код на оплату
         * 2. Партнёр открывает вкладку с другим адресом. и там происходит редирект либо в плате, либо в тест.
         * ---
         * Токен можно сделать так, чтобы было понятно, что это токен оплаты или токен доступа.
         * Например, добавлять в хвост ==p или ==a
         *
         * Payment или ProviderPayment. Однажды тут будет тоже платежный механизм. Не хотелось бы плодить разные штуки.
         * Тогда может быть один Payment(id, created_at, provider_payment(null), executed_at)
         * и ProviderPayment сделать уже отдельной таблицей
         * Access
         */
        $providerPayment = $this->providerPaymentService->findOneByProviderAndUser($provider, $user);
        if ($providerPayment == null) {
            $providerPayment = ProviderPayment::init($provider, $user);
            $providerPayment = $this->providerPaymentService->save($providerPayment);
            return $providerPayment;
        } elseif (!$providerPayment->isExecuted()) {
            return $providerPayment;
        }

        return $this->accessService->createWithProvider($provider);
    }
}