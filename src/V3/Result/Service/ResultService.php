<?php

declare(strict_types=1);

namespace App\V3\Result\Service;

use App\Entity\Result;
use App\Service\CalculatorService;
use App\Test\ResultRenderer;
use App\Test\ViewFormat;
use Symfony\Component\HttpFoundation\Response;

class ResultService
{
    public function __construct(
        private readonly CalculatorService $calculatorService,
        private readonly ResultRenderer    $resultRenderer
    )
    {
    }

    public function getResultResponse(Result $result, ViewFormat $format): Response
    {
        $calculatedData = $this->calculatorService->calculate($result);
        return $this->resultRenderer->render($result->getTest(), $calculatedData, $format);
    }
}