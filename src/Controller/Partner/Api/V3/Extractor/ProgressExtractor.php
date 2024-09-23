<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3\Extractor;

use App\Test\Progress\Progress;
use App\V3\Progress\RawAnswersToProgressConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

final class ProgressExtractor
{
    public function extract(Request $request, string $paramName): Progress
    {
        $answers = $request->get($paramName);

        if (!$answers) {
            throw new BadRequestException('The required "' . $paramName . '" parameter is missing.');
        }

        if (!is_array($answers)) {
            throw new BadRequestException('The "' . $paramName . '" parameter must be in array format.');
        }

        return (new RawAnswersToProgressConverter())->convert($answers);
    }
}