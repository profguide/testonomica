<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

final class CalculationTypesValues
{
    /**
     * @var Values[]
     */
    private array $values;

    public function add(string $type, Values $values)
    {
        $this->values[$type] = $values;
    }

    public function get(string $type): Values
    {
        return $this->values[$type];
    }

    public function all(): array
    {
        return $this->values;
    }
}