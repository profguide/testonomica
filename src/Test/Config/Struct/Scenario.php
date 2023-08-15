<?php

declare(strict_types=1);

namespace App\Test\Config\Struct;

use App\Test\Config\Struct\Condition\Condition;
use App\Test\Config\Struct\Scale\Scale;

final readonly class Scenario
{
    /**
     * @param Condition[] $conditions
     * @param string $text
     * @param ?Scale $scale
     */
    public function __construct(public array $conditions, public string $text, public ?Scale $scale)
    {
    }
}