<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3\Extractor;

use App\Entity\Result;
use App\Exception\ResultNotFoundException;
use App\Repository\ResultRepository;
use App\Test\Result\ResultKeyFactory;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * not final and readonly for mocking
 */
class ResultExtractor
{
    public function __construct(
        private readonly ResultRepository $resultRepository,
        private readonly ResultKeyFactory $resultKeyFactory)
    {
    }

    public function extract(Request $request, string $paramName): Result
    {
        $key = $request->get($paramName);

        if (!$key) {
            throw new BadRequestException("Missing the required parameter \"$paramName\".'");
        }

        $resultKey = $this->resultKeyFactory->create($key);
        $result = $this->resultRepository->findByKey($resultKey);

        if (!$result) {
            throw new ResultNotFoundException("Result \"$key\" not found.");
        }

        return $result;
    }
}