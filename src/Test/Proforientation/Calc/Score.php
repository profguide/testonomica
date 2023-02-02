<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

final class Score
{
    private float $value;

    private array $log;

    public function __construct(float $value, array $log = [])
    {
        $this->value = $value;
        $this->log = $log;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function log(): array
    {
        return $this->log;
    }
}