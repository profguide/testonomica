<?php

declare(strict_types=1);

namespace App\Tests\Controller\Partner\Api\V2;

use App\Entity\ProviderUser;
use App\Entity\Result;
use App\Entity\Test;
use App\Repository\ProviderUserRepository;
use App\Repository\TestRepository;
use App\V2\Provider\Command\RegisterUser\RegisterProviderUserHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class ProgressControllerTest extends WebTestCase
{
    /**
     * Проверяет, что URL доступен, что параметры запроса корректно распознаются, и что ответ - json с id результата
     *
     * Ожидаемый результат:
     * - возвращён json, содержащий id результата (прогресса)
     */
    public function testSuccess()
    {
        // Arrange
        $expectedResult = new Result();
        $expectedResult->setUuid('result-code');
//        $expectedResult->setId(new Uuid('018ad6f5-7164-7568-8a00-a95cfc8958c9'));

        $testRepository = $this->createMock(TestRepository::class);
        $testRepository
            ->method('findOneBySlug')
            ->willReturn(new Test());

        $userRepository = $this->createMock(ProviderUserRepository::class);
        $userRepository
            ->method('find')
            ->willReturn(new ProviderUser());

        $handledStamp = new HandledStamp($expectedResult, RegisterProviderUserHandler::class);
        $messageBusMock = $this->createMock(MessageBusInterface::class);
        $messageBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope(new \stdClass(), [$handledStamp]));

        // Act
        $client = self::createClient();
        $container = $client->getContainer();

        // Внедрение зависимостей
        $container->set('Symfony\Component\Messenger\MessageBusInterface', $messageBusMock);
        $container->set(ProviderUserRepository::class, $userRepository);
        $container->set(TestRepository::class, $testRepository);

        $client->request('GET', '/partner/api/v2/progress/save', [
            'test' => 'test-slug',
            'user_key' => '018ad6f5-7164-7568-8a00-a95cfc8958c9',
            'progress' => [['1', ['a']], ['2', ['b', 'c']]]
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('{"result_key":"result-code"}', $client->getResponse()->getContent());
    }
}