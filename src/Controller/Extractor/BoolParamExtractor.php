<?php

declare(strict_types=1);

namespace App\Controller\Extractor;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

class BoolParamExtractor
{
    public function extract(Request $request, string $paramName, bool $required = false): bool|null
    {
        $value = $request->get($paramName);
        if ($value === null) {
            return $required
                ? throw new BadRequestException("Missing \"$paramName\" parameter.", 400)
                : null;
        }

        return match ($value) {
            'true' => true,
            'false' => false,
            default => throw new BadRequestException("Invalid value for \"$paramName\". Only \"true\" or \"false\" is expected.", 400)
        };
    }
}