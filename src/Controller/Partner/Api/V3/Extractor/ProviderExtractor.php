<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3\Extractor;

use App\Entity\Provider;
use App\Exception\ProviderNotFoundException;
use App\Repository\ProviderRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

class ProviderExtractor
{
    public function __construct(private readonly ProviderRepository $providers)
    {
    }

    public function extract(Request $request, string $paramName): Provider
    {
        $key = $request->get($paramName);

        if (!$key) {
            throw new BadRequestException("Missing the required parameter \"$paramName\".'");
        }

        $client = $this->providers->getByToken($key);
        if (!$client) {
            throw new ProviderNotFoundException("The client \"$key\" not found.");
        }

        return $client;
    }
}