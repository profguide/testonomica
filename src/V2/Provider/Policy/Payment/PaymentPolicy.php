<?php

declare(strict_types=1);

namespace App\V2\Provider\Policy\Payment;

enum PaymentPolicy: string
{
    case PRE = 'pre';

    case POST = 'post';

    public function rus()
    {
        return $this->value === self::PRE->value ? 'Предоплата' : 'Постоплата';
    }
}