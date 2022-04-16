<?php

declare(strict_types=1);

namespace App\Entity;

class PaymentType
{
    const INTERNAL = 0;
    const EXTERNAL = 1;
    const DEFAULT = self::INTERNAL;

    private int $value;

    public function __construct(int $value)
    {
        if (!in_array($value, [self::INTERNAL, self::EXTERNAL])) {
            throw new \DomainException("Unsupported payment type: $value.");
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function is(int $value): bool
    {
        return $this->value === $value;
    }
}