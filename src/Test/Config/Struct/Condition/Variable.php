<?php

declare(strict_types=1);

namespace App\Test\Config\Struct\Condition;

enum Variable: string
{
    case SUM = 'sum';

    case SCALE = 'scale';

    public static function fromString(string $value): Variable
    {
        return match ($value) {
            'sum' => Variable::SUM,
            'scale' => Variable::SCALE,
            default => throw new \DomainException("Unsupported variable \"$value\".")
        };
    }
}