<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3\Extractor;

use App\Entity\Test;
use App\Exception\TestNotFoundException;
use App\Repository\TestRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

class TestExtractor
{
    public function __construct(private TestRepository $tests)
    {
    }

    public function extract(Request $request, string $paramName): Test
    {
        $slug = $request->get($paramName);

        if (!$slug) {
            throw new BadRequestException('The required "' . $paramName . '" parameter is missing.');
        }

        $test = $this->tests->findOneBySlug($slug);
        if (!$test) {
            throw new TestNotFoundException("Test not found with the provided \"" . $paramName . "\" value: \"$slug\".");
        }

        return $test;
    }
}