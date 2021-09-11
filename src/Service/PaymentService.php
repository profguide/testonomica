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

class PaymentService
{
    private PaymentRepository $repository;

    /**
     * PaymentService constructor.
     * @param PaymentRepository $repository
     */
    public function __construct(PaymentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOneById($id): Payment
    {
        /**@var Payment $payment */
        $payment = $this->repository->find($id);
        return $payment;
    }

    public function create(Service $service, int $price, bool $testMode): Payment
    {
        return $this->repository->save(Payment::init($service, $price, $testMode));
    }

    public function save($payment): Payment
    {
        return $this->repository->save($payment);
    }

    public function setCookie(Payment $payment, Response $response)
    {
        $cookie = Cookie::create('payment', $payment->getId(), time() + 60 * 60 * 24 * 365)
            ->withHttpOnly(false)
            ->withSameSite(null);
        $response->headers->setCookie($cookie);
    }

    public function getCookie(Request $request)
    {
        return $request->cookies->get('payment');
    }
}