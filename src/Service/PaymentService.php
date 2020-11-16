<?php
/**
 * @author: adavydov
 * @since: 12.11.2020
 */

namespace App\Service;


use App\Entity\Payment;
use App\Repository\PaymentRepository;

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

    public function create(int $price)
    {
        return $this->repository->save(Payment::init($price));
    }
}