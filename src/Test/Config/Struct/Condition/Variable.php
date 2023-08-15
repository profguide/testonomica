<?php

declare(strict_types=1);

namespace App\Test\Config\Struct\Condition;

final readonly class Variable
{
    public function __construct(public string $value)
    {
    }
}