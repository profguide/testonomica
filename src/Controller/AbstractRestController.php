<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractRestController extends AbstractController
{
    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $response = parent::json($data, $status, $headers, $context);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $response;
    }
}