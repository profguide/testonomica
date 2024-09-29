<?php

declare(strict_types=1);

namespace App\Tests\Controller\Partner\Api\V3;

use App\Controller\Partner\Api\V3\Extractor\ProviderExtractor;
use App\Entity\Provider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ClientProviderTest extends WebTestCase
{
    public function testSuccess()
    {
        // Arrange
        $client = self::createClient();

        $provider = $this->createMock(Provider::class);
        $provider->method('getName')->willReturn('Test Provider');
        $provider->method('getToken')->willReturn('token-123');

        $providerExtractor = $this->createMock(ProviderExtractor::class);
        $providerExtractor->method('extract')->willReturn($provider);
        $client->getContainer()->set(ProviderExtractor::class, $providerExtractor);

        // Act
        $client->request('POST', '/partner/api/v3/client/authenticate-by-token', [
            'client_key' => 'token-123',
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('{"name":"Test Provider","key":"token-123"}', $client->getResponse()->getContent());
    }
}