<?php

declare(strict_types=1);

namespace App\Tests\Controller\Partner\Api\V3\Extractor;

use App\Controller\Partner\Api\V3\Extractor\ProviderExtractor;
use App\Entity\Provider;
use App\Exception\ProviderNotFoundException;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

final class ProviderExtractorTest extends KernelTestCase
{
    private ProviderRepository $providerRepository;
    private ProviderExtractor $providerExtractor;

    protected function setUp(): void
    {
        $this->providerRepository = $this->createMock(ProviderRepository::class);
        $this->providerExtractor = new ProviderExtractor($this->providerRepository);
    }

    public function testExtractFromGetParameter(): void
    {
        $provider = new Provider();
        $this->providerRepository
            ->method('getByToken')
            ->with('HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL')
            ->willReturn($provider);

        $request = new Request(['client_key' => 'HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL']);

        $result = $this->providerExtractor->extract($request, 'client_key');

        $this->assertSame($provider, $result);
    }

    public function testExtractFromPostParameter(): void
    {
        $provider = new Provider();
        $this->providerRepository
            ->method('getByToken')
            ->with('HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL')
            ->willReturn($provider);

        $request = new Request([], ['client_key' => 'HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL']);

        $result = $this->providerExtractor->extract($request, 'client_key');

        $this->assertSame($provider, $result);
    }

    public function testExtractFromJsonBody(): void
    {
        $provider = new Provider();
        $this->providerRepository
            ->method('getByToken')
            ->with('HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL')
            ->willReturn($provider);

        $request = new Request([], [], [], [], [], [], json_encode(['client_key' => 'HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL']));

        $result = $this->providerExtractor->extract($request, 'client_key');

        $this->assertSame($provider, $result);
    }

    public function testMissingParamThrowsBadRequestException(): void
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Missing the required parameter "client_key".');
        $this->expectExceptionCode(400);

        $request = new Request();

        $this->providerExtractor->extract($request, 'client_key');
    }

    public function testProviderNotFoundThrowsException(): void
    {
        $this->providerRepository
            ->method('getByToken')
            ->with('HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL')
            ->willReturn(null);

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage('The client "HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL" not found.');
        $this->expectExceptionCode(401);

        $request = new Request(['client_key' => 'HpMGQ9jtdvP0ZEHG62YXJCfZnDekeOUL']);

        $this->providerExtractor->extract($request, 'client_key');
    }
}