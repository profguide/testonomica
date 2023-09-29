<?php

declare(strict_types=1);

namespace App\Tests\Controller\Partner\Api\V2;

use App\Entity\Provider;
use App\Entity\ProviderUser;
use App\Repository\ProviderRepository;
use App\V2\Provider\Command\RegisterUser\RegisterProviderUserHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Uid\Uuid;

final class UserControllerTest extends WebTestCase
{
    /**
     * Проверяет, что URL доступен, корректно указанные параметры запроса валидны и результат в нужном формате
     *
     * Ожидаемый результат:
     * - возвращён json, содержащий ID пользователя
     */
    public function testSuccess()
    {
        // Arrange
        $provider = new Provider();
        $expectedUser = ProviderUser::create($provider, '555');
        $expectedUser->setId(new Uuid('018ad6f5-7164-7568-8a00-a95cfc8958c9'));

        $providerRepositoryMock = $this->createMock(ProviderRepository::class);
        $providerRepositoryMock
            ->method('getByToken')
            ->willReturn($provider);

        $handledStamp = new HandledStamp($expectedUser, RegisterProviderUserHandler::class);
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
        $container->set('App\Repository\ProviderRepository', $providerRepositoryMock);

        $client->request('GET', '/partner/api/v2/user/register', [
            'client' => 'client-token',
            'user_id' => '555',
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('{"user_key":"018ad6f5-7164-7568-8a00-a95cfc8958c9"}', $client->getResponse()->getContent());
    }
}