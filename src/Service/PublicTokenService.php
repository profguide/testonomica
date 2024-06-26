<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Service;


use App\Entity\Access;
use App\Entity\PaymentType;
use App\Entity\Provider;
use App\Entity\ProviderPayment;
use App\Entity\Service;
use App\Payment\TokenableInterface;

/**
 * Отвечает за создание пуличных токенов.
 *
 * Class PublicTokenService
 * @package App\Service
 */
class PublicTokenService
{
    private ProviderUserPaymentService $providerUserPaymentService;

    private AccessService $accessService;

    public function __construct(ProviderUserPaymentService $providerUserPaymentService, AccessService $accessService)
    {
        $this->providerUserPaymentService = $providerUserPaymentService;
        $this->accessService = $accessService;
    }

    public function find(string $token): ?TokenableInterface
    {
        $access = $this->accessService->findOneByToken($token);
        if ($access) {
            return $access;
        }
        $payment = $this->providerUserPaymentService->findOneByToken($token);
        if ($payment) {
            return $payment;
        }

        return null;
    }

    /**
     * Публичный токен
     * В зависимости от того, оплатил ли пользователь:
     * - создаёт/находит_созданный постоянный токен оплаты
     * - создаёт одноразовый токен доступа
     *
     * @param Service $service
     * @param Provider $provider
     * @param string $user
     * @param PaymentType $paymentType
     * @param bool $testMode
     * @return TokenableInterface
     */
    public function token(Service $service, Provider $provider, string $user, PaymentType $paymentType, bool $testMode = false): TokenableInterface
    {
        if ($this->isPaid($provider, $user, $service)) {
            return $this->accessToken($service);
        } else {
            // todo check
            if ($paymentType->is(PaymentType::EXTERNAL)) {
                // Публичный токен доступа с доверительным платежом
                // Партнёр принимает оплату на своей стороне
                // Создаём доверительный платёж сразу оплаченным
                $this->providerUserPaymentService->createTrust($provider, $user, $service);
                return $this->accessToken($service);
            }
            return $this->paymentToken($service, $provider, $user, $testMode);
        }
    }

    /**
     * Создаёт токен доступа, пометив платёжный токен как использованный.
     * @param ProviderPayment $providerPayment
     * @return Access|null
     */
    public function createFirstAccessAfterPayment(ProviderPayment $providerPayment): ?Access
    {
        $providerPayment->setGrantedAccess();
        $this->providerUserPaymentService->save($providerPayment);
        return $this->accessService->create($providerPayment->getPayment()->getService());
//        $this->flusher->flush();
    }

    private function isPaid(Provider $provider, string $user, Service $service): bool
    {
        return $this->providerUserPaymentService->hasExecutedPayment($provider, $user);
    }

    private function accessToken(Service $service): Access
    {
        return $this->accessService->create($service);
    }

    private function paymentToken(Service $service, Provider $provider, string $user, bool $testMode): ProviderPayment
    {
        return $this->providerUserPaymentService->create($provider, $user, $service, $testMode);
    }
}