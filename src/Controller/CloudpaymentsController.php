<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Payment;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("cloudpayments", name="cloudpayments.")
 */
class CloudpaymentsController extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @Route ("pay", name="pay")
     * @param Request $request
     * @return Response
     */
    public function success(Request $request): Response
    {
        $payment = $this->payment($request);
        $this->paymentService->execute($payment);
        return new Response('{"code":0}');
    }

    /**
     * @Route ("fail", name="fail")
     * @param Request $request
     * @return Response
     */
    public function fail(Request $request): Response
    {
        $payment = $this->payment($request);
        $this->paymentService->fail($payment);
        return new Response('{"code":0}');
    }

    //

    private function payment(Request $request): Payment
    {
        $id = self::id($request);
        return $this->paymentService->getOneById($id);
    }

    private static function id(Request $request)
    {
        $id = $request->get('InvoiceId');
        if (!$id) {
            throw new \RuntimeException("InvoiceId is not specified.");
        }
        return $id;
    }
}