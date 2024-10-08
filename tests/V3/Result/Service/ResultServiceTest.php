<?php

declare(strict_types=1);

namespace App\Tests\V3\Result\Service;

use App\Entity\Result;
use App\Service\CalculatorService;
use App\Test\ResultRenderer;
use App\Test\ViewFormat;
use App\V3\Result\Service\ResultAccessControlService;
use App\V3\Result\Service\ResultService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;

// generated by gpt
class ResultServiceTest extends KernelTestCase
{
    private ResultAccessControlService $accessControlService;
    private CalculatorService $calculatorService;
    private ResultRenderer $resultRenderer;
    private ResultService $resultService;

    protected function setUp(): void
    {
        $this->accessControlService = $this->createMock(ResultAccessControlService::class);
        $this->calculatorService = $this->createMock(CalculatorService::class);
        $this->resultRenderer = $this->createMock(ResultRenderer::class);

        $this->resultService = new ResultService(
            $this->accessControlService,
            $this->calculatorService,
            $this->resultRenderer
        );
    }

    public function testGetCheckedResultResponseCallsAccessControlAndRendersResult(): void
    {
        $result = $this->createMock(Result::class);
        $format = $this->createMock(ViewFormat::class);
        $calculatedData = ['data'];

        // Проверяем, что guardResultAccess вызывается
        $this->accessControlService
            ->expects($this->once())
            ->method('guardResultAccess')
            ->with($result);

        // Проверяем вызов калькуляции
        $this->calculatorService
            ->expects($this->once())
            ->method('calculate')
            ->with($result)
            ->willReturn($calculatedData);

        // Проверяем рендеринг
        $this->resultRenderer
            ->expects($this->once())
            ->method('render')
            ->with($result->getTest(), $calculatedData, $format)
            ->willReturn(new Response('Rendered result'));

        // Вызов метода
        $response = $this->resultService->getCheckedResultResponse($result, $format);

        // Проверяем, что вернулся правильный Response
        $this->assertEquals('Rendered result', $response->getContent());
    }
}
