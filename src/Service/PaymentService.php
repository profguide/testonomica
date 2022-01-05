<?php
/**
 * @author: adavydov
 * @since: 12.11.2020
 */

namespace App\Service;

use App\Entity\Payment;
use App\Entity\Service;
use App\Repository\PaymentRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * todo описать для чего нужен класс, и чем отличается от ProviderPaymentService
 * Class PaymentService
 * @package App\Service
 */
class PaymentService
{
    private PaymentRepository $repository;

    public function __construct(PaymentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getOneById($id): Payment
    {
        /**@var Payment $payment */
        $payment = $this->repository->find($id);
        if ($payment) {
            return $payment;
        }
        throw new \DomainException("Payment id#$id not found.");
    }

    public function create(Service $service, bool $testMode): Payment
    {
        return $this->repository->save(Payment::init($service, $service->getSum(), $testMode));
    }

    public function save($payment): Payment
    {
        return $this->repository->save($payment);
    }

    // todo не думаю, что кука должна быть установлена здесь. Для этого должен быть сервис кук.
    public function setCookie(Payment $payment, Response $response)
    {
        // без sameSite=none прочитать куку после редиректа с робокассы будет нельзя. это делают браузеры
        // из соображений безопасности (они не отправляют куки и в Request их нет).
        $cookie = Cookie::create('payment', $payment->getId(), time() + 60 * 60 * 24 * 365)
            ->withHttpOnly(false)
            ->withSameSite(Cookie::SAMESITE_NONE);
        $response->headers->setCookie($cookie);
    }

    public function getCookie(Request $request)
    {
        return $request->cookies->get('payment');
    }
}