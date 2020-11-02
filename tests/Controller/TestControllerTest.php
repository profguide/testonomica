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
use App\Repository\ResultRepository;
use App\Repository\TestRepositoryInterface;
use App\Test\AnswersSerializer;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
    /**@var TestRepositoryInterface */
    private $testRepository;

    /**@var ResultRepository */
    private $resultRepository;

    /**@var AnswersSerializer */
    private $serializer;

    /**@var KernelBrowser */
    private $client;

    /**@var Test */
    private $test;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->testRepository = self::$container->get(TestRepositoryInterface::class);
        $this->resultRepository = self::$container->get(ResultRepository::class);
        $this->serializer = self::$container->get(AnswersSerializer::class);
        $this->test = $this->testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    /**
     * Страница результата
     * Ожидаем, что страница найдена
     * todo проверить содержимое, когда кальтулятор будет готов
     */
    public function testResult()
    {
        $result = $this->initResult();
        $this->resultRepository->save($result);
        $this->client->request('POST', "/tests/result/{$result->getUuid()}/");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    private function initResult()
    {
        $answer = Answer::create("1", "my-answer");
        $serialized = $this->serializer->serialize([$answer]);
        return Result::create(
            $this->test,
            '00000000-0000-0000-0000-000000000',
            $serialized);
    }
}