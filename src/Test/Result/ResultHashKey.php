<?php

declare(strict_types=1);

namespace App\Test\Result;

final readonly class ResultHashKey implements ResultKey
{
    public function __construct(private string $value)
    {
        if (empty($value)) {
            throw new \DomainException("Result key can not be empty.");
        }
    }

    function getValue(): string
    {
        return $this->value;
    }
}