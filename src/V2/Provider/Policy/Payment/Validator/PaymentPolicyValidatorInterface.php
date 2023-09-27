<?php

namespace App\V2\Provider\Policy\Payment\Validator;

use App\Entity\Provider;

interface PaymentPolicyValidatorInterface
{
    public function validate(Provider $provider): bool;

    public function getMessage(): string;
}