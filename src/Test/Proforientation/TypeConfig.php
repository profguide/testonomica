<?php

declare(strict_types=1);

namespace App\Test\Proforientation;

use App\Test\Proforientation\Type\Interest;
use App\Test\Proforientation\Type\Skill;

final class TypeConfig
{
    private string $name;

    private Interest $interest;

    private Skill $skill;

    public function __construct($name, $interest, $skill)
    {
        $this->name = $name;
        $this->interest = $interest;
        $this->skill = $skill;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function interest(): Interest
    {
        return $this->interest;
    }

    public function skill(): Skill
    {
        return $this->skill;
    }
}