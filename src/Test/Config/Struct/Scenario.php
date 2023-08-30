<?php

declare(strict_types=1);

namespace App\Test\Config\Struct;

use App\Test\Config\Struct\Scale\Scale;

final readonly class Scenario
{
    public function __construct(public array $conditions, public ?string $name, public ?string $text, public ?Scale $scale)
    {
    }
}