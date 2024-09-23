<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3\Extractor;

use App\Test\ViewFormat;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

final class ViewFormatExtractor
{
    public function extract(Request $request, string $paramName): ViewFormat
    {
        $format = $request->get($paramName);

        if (!$format) {
            throw new BadRequestException('The required "' . $paramName . '" parameter is missing.');
        }

        if (!in_array($format, [ViewFormat::JSON, ViewFormat::PDF, ViewFormat::HTML])) {
            throw new BadRequestException("Unsupported \"" . $paramName . "\" value: \"$format\".");
        }

        return new ViewFormat($format);
    }
}