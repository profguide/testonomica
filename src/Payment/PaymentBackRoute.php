<?php

declare(strict_types=1);

namespace App\Payment;

class PaymentBackRoute
{
    const TEST_VIEW = 'view';
    const TEST_WIDGET = 'widget'; // todo раньше был виджет, теперь его нет

    private ?string $value = null;

    public function __construct(?string $value)
    {
        if (empty($value)) {
            return;
        }
        if (!in_array($value, [self::TEST_VIEW, self::TEST_WIDGET])) {
            throw new \DomainException("Unsupported value for back route: $value.");
        }
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}