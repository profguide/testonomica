<?php

declare(strict_types=1);

namespace App\V2\Provider\Policy\Payment\Validator;

use App\Tests\V2\Provider\Policy\Payment\Validator\PaymentPolicyValidatorFactoryTest;
use App\V2\Provider\Policy\Payment\PaymentPolicy;

/**
 * @see PaymentPolicyValidatorFactoryTest
 */
final class PaymentPolicyValidatorFactory
{
    public function createValidator(PaymentPolicy $policy): PaymentPolicyValidatorInterface
    {
        return match ($policy) {
            PaymentPolicy::PRE => new PrepaidPaymentPolicyValidator(),
            PaymentPolicy::POST => new PostpaidPaymentPolicyValidator(),
            default => throw new \InvalidArgumentException("Unknown payment policy \"$policy->name\"."),
        };
    }
}