<?php
/**
 * @author: adavydov
 * @since: 12.11.2020
 */

namespace App\Service;


use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class PaymentService
{
    /**@var PaymentRepository */
    private $repository;

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

    public function create(int $price): Payment
    {
        return $this->repository->save(Payment::init($price));
    }

    public function save($payment): Payment
    {
        return $this->repository->save($payment);
    }

    public function saveToCookie(Payment $payment, Response $response)
    {
        $response->headers->setCookie(Cookie::create('payment', $payment->getId(), 60 * 60 * 24 * 365));
    }
}