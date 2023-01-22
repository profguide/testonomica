<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

final class Values
{
    private float $force;
    private float $interest;
    private float $skills;

    public function __construct(float $force, float $interest, float $skills)
    {
        $this->force = $force;
        $this->interest = $interest;
        $this->skills = $skills;
    }

    public function force(): float
    {
        return $this->force;
    }

    public function interest(): float
    {
        return $this->interest;
    }

    public function skills(): float
    {
        return $this->skills;
    }
}