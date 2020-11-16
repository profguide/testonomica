<?php
/**
 * @author: adavydov
 * @since: 13.11.2020
 */

namespace App\Payment;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PaymentNotExecutedException extends AccessDeniedHttpException
{
}