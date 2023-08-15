<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\TestFixture;
use App\Entity\Answer;
use App\Entity\Result;
use App\Entity\Test;
use App\Repository\TestRepositoryInterface;
use App\Service\ResultService;
use App\Test\AnswersSerializer;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
    /**@var TestRepositoryInterface */
    private $testRepository;

    /**@var ResultService */
    private $resultService;

    /**@var AnswersSerializer */
    private $serializer;

    /**@var KernelBrowser */
    private $client;

    /**@var Test */
    private $test;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->testRepository = static::getContainer()->get(TestRepositoryInterface::class);
        $this->resultService = static::getContainer()->get(ResultService::class);
        $this->serializer = static::getContainer()->get(AnswersSerializer::class);
        $this->test = $this->testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    public function testViewPayableTestWithoutAccess()
    {
        $this->requestView();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Страница результата
     * Ожидаем, что страница найдена
     */
    public function testResult()
    {
        $result = $this->initResult();
        $this->resultService->save($result);
        $this->client->request('POST', "/tests/result/{$result->getUuid()}/");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    private function initResult(): Result
    {
        $answer = new Answer("1", ["my-answer"]);
        $serialized = $this->serializer->serialize([$answer]);
        return Result::create(
            $this->test,
            '00000000-0000-0000-0000-000000000',
            $serialized);
    }

    private function requestView()
    {
        $testSlug = $this->test->getSlug();
        $this->client->request('POST', "/tests/view/$testSlug/");
    }
}