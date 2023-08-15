<?php

declare(strict_types=1);

namespace App\Test\Config\Struct\Condition;

enum Operator
{
    case GREATER;

    case GREATER_OR_EQUAL;

    case LOWER;

    case LOWER_OR_EQUAL;

    case EQUAL;

    public static function fromValue(string $value): Operator
    {
        return match ($value) {
            'больше' => Operator::GREATER,
            'больше_или_равно' => Operator::GREATER_OR_EQUAL,
            'меньше' => Operator::LOWER,
            'меньше_или_равно' => Operator::LOWER_OR_EQUAL,
            'равно' => Operator::EQUAL,
            '>' => Operator::GREATER,
            '>=' => Operator::GREATER_OR_EQUAL,
            '<' => Operator::LOWER,
            '<=' => Operator::LOWER_OR_EQUAL,
            '=' => Operator::EQUAL,
            default => throw new \DomainException("Unsupported operator \"$value\".")
        };
    }
}