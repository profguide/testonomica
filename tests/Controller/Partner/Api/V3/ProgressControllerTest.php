<?php

declare(strict_types=1);

namespace App\Tests\Controller\Partner\Api\V3;

use App\Controller\Partner\Api\V3\Extractor\TestExtractor;
use App\Entity\Result;
use App\Entity\Test;
use App\V3\Result\Service\ProgressService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

final class ProgressControllerTest extends WebTestCase
{
    /**
     * Проверяет только вывод потому что в контроллере нет никакой бизнес логики.
     *
     * Ожидаемый результат:
     * - возвращён json, содержащий id результата (прогресса)
     */
    public function testSuccess()
    {
        // Arrange
        $client = self::createClient();

        $testExtractor = $this->createMock(TestExtractor::class);
        $testExtractor->method('extract')->willReturn(new Test());
        $client->getContainer()->set(TestExtractor::class, $testExtractor);

        $resultToReturn = new Result();
        $resultToReturn->setNewId(new Uuid("018ad6f5-7164-7568-8a00-a95cfc8958c9"));
        $progressService = $this->createMock(ProgressService::class);
        $progressService->method('saveProgress')->willReturn($resultToReturn);
        $client->getContainer()->set(ProgressService::class, $progressService);

        // Act
        $client->request('POST', '/partner/api/v3/progress/save', [
            'test' => 'test-slug',
            'progress' => [['1', ['a']], ['2', ['b', 'c']]]
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('{"result_key":"018ad6f5-7164-7568-8a00-a95cfc8958c9"}', $client->getResponse()->getContent());
    }
}