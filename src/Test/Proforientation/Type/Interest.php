<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Type;

final class Interest
{
    private string $minText;
    private string $midText;
    private string $maxText;

    public function __construct($minText, $midText, $maxText)
    {
        $this->minText = $minText;
        $this->midText = $midText;
        $this->maxText = $maxText;
    }

    public function minText(): string
    {
        return $this->minText;
    }

    public function midText(): string
    {
        return $this->midText;
    }

    public function maxText(): string
    {
        return $this->maxText;
    }
}