<?php

declare(strict_types=1);

namespace App\Tests\Controller\Partner\Api\V3;

use App\Controller\Partner\Api\V3\Extractor\ProviderExtractor;
use App\Controller\Partner\Api\V3\Extractor\ResultExtractor;
use App\Controller\Partner\Api\V3\Extractor\UserIdExtractor;
use App\Entity\Provider;
use App\Entity\Result;
use App\V3\Result\Service\ResultAttachmentService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ResultAttachControllerTest extends WebTestCase
{
    public function testResponse()
    {
        $client = self::createClient();

        $providerExtractor = $this->createMock(ProviderExtractor::class);
        $providerExtractor->method('extract')->willReturn(new Provider());
        $client->getContainer()->set(ProviderExtractor::class, $providerExtractor);

        $resultExtractor = $this->createMock(ResultExtractor::class);
        $resultExtractor->method('extract')->willReturn(new Result());
        $client->getContainer()->set(ResultExtractor::class, $resultExtractor);

        $userExtractor = $this->createMock(UserIdExtractor::class);
        $userExtractor->method('extract')->willReturn('123-123');
        $client->getContainer()->set(UserIdExtractor::class, $userExtractor);

        $attachmentService = $this->createMock(ResultAttachmentService::class);
        $client->getContainer()->set(ResultAttachmentService::class, $attachmentService);

        $client->request('POST', '/partner/api/v3/result/attach', [
            'client_key' => 'uk1',
            'user_id' => '123-123',
            'result_key' => '999',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            '{"status":true}',
            $client->getResponse()->getContent()
        );
    }
}