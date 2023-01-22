<?php

declare(strict_types=1);

namespace App\Test\Proforientation;

final class Types
{
    /**
     * @var TypesCombination[]
     */
    private array $combinations = [];

    public function __construct(array $types)
    {
        $this->combinations = $types;
    }

    public function combinations(): array
    {
        return $this->combinations;
    }
}