<?php

declare(strict_types=1);

namespace App\Test\Config\Struct\Scale;

final readonly class Level
{
    const ALLOWED_COLORS = [
        'primary', 'success', 'warning', 'danger'
    ];

    public function __construct(public int $upTo, public string $color)
    {
        self::guardTo($upTo);
        self::guardColor($this->color);
    }

    private static function guardTo(int $upTo): void
    {
        if ($upTo < 0 || $upTo > 100) {
            throw new \DomainException("The value must be from 0 to 100, \"$upTo\" given.");
        }
    }

    private static function guardColor(string $color): void
    {
        if (!in_array($color, self::ALLOWED_COLORS)) {
            throw new \DomainException("Unsupported color \"$color\".");
        }
    }
}