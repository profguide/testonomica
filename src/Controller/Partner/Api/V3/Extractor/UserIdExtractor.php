<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3\Extractor;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

class UserIdExtractor
{
    public function extract(Request $request, string $paramName): string
    {
        $id = $request->get($paramName);

        if (!$id) {
            throw new BadRequestException('The required "' . $paramName . '" parameter is missing.');
        }

        return $id;
    }
}