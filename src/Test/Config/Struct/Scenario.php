<?php

declare(strict_types=1);

namespace App\Test\Config\Struct;

use App\Test\Config\Struct\Condition\Condition;

final readonly class Scenario
{
    /***
     * @param Condition[] $conditions
     * @param string $text
     */
    public function __construct(public array $conditions, public string $text)
    {
    }
}