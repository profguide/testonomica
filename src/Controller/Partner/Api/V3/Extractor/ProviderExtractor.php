<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3\Extractor;

use App\Entity\Provider;
use App\Exception\ProviderNotFoundException;
use App\Repository\ProviderRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see ProviderExtractorTest
 */
class ProviderExtractor extends AbstractParamExtractor
{
    public function __construct(private readonly ProviderRepository $providers)
    {
    }

    public function extract(Request $request, string $paramName): Provider
    {
        $key = $this->getStringRequestParam($request, $paramName);

        if (!$key) {
            throw new BadRequestException("Missing the required parameter \"$paramName\".'", 400);
        }

        $client = $this->providers->getByToken($key);
        if (!$client) {
            throw new ProviderNotFoundException("The client \"$key\" not found.", 401);
        }

        return $client;
    }
}