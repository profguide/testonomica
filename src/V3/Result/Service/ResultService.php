<?php

declare(strict_types=1);

namespace App\V3\Result\Service;

use App\Entity\Result;
use App\Service\CalculatorService;
use App\Test\ResultRenderer;
use App\Test\ViewFormat;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see \App\Tests\V3\Result\Service\ResultServiceTest
 */
class ResultService
{
    public function __construct(
        private readonly ResultAccessControlService $accessControlService,
        private readonly CalculatorService          $calculatorService,
        private readonly ResultRenderer             $resultRenderer
    )
    {
    }

    /**
     * Checks in advance whether the result is available for display.
     *
     * @param Result $result
     * @param ViewFormat $format
     * @return Response
     */
    public function getCheckedResultResponse(Result $result, ViewFormat $format): Response
    {
        $this->accessControlService->guardResultAccess($result);

        $calculatedData = $this->calculatorService->calculate($result);
        return $this->resultRenderer->render($result->getTest(), $calculatedData, $format);
    }
}