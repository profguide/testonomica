<?php

declare(strict_types=1);

namespace App\V2\Provider\Policy\Payment\Validator;

use App\Entity\Provider;

/**
 * @see PostpaidPaymentPolicyValidatorTest
 */
final class PostpaidPaymentPolicyValidator implements PaymentPolicyValidatorInterface
{
    /**
     * На данный момент валидатор ничего не проверяет,
     * хотя в будущем это может измениться,
     * например, может потребоваться проверить лимит долга текущего месяца.
     */
    public function validate(Provider $provider): bool
    {
        return true;
    }

    public function getMessage(): string
    {
        return '';
    }
}