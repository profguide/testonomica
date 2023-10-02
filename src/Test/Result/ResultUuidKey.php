<?php

declare(strict_types=1);

namespace App\Test\Result;

use Symfony\Component\Uid\Uuid;

final readonly class ResultUuidKey implements ResultKey
{
    public function __construct(private Uuid $value)
    {
    }

    function getValue(): Uuid
    {
        return $this->value;
    }
}