<?php

declare(strict_types=1);

namespace App\V2\Provider\Policy\Payment\Validator;

use App\Entity\Provider;

/**
 * @see PrepaidPaymentPolicyValidatorTest
 */
final class PrepaidPaymentPolicyValidator implements PaymentPolicyValidatorInterface
{
    const ERROR_MESSAGE_LIMIT_REACHED = 'Достигнут предел выданных доступов.';

    private ?string $message;

    public function validate(Provider $provider): bool
    {
        $providedCount = $provider->getAccessCount();
        $limit = $provider->getAccessLimit();

        // 0 >= 0 = limit reached
        // 1000 >= 1000 = limit unreached
        // 1000 >= 999 = limit reached
        if ($providedCount >= $limit) {
            $this->message = self::ERROR_MESSAGE_LIMIT_REACHED;
            return false;
        }

        return true;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}