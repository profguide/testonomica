<?php

declare(strict_types=1);

namespace App\Test\Config\Struct\Condition;

final readonly class Condition
{
    public function __construct(public Variable $varName, public Operator $operator, public string $value)
    {
    }
}