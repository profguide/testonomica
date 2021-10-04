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
use App\Service\AnswerService;
use App\Service\ResultService;
use App\Test\AnswersSerializer;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class TestControllerTest extends WebTestCase
{
    /**@var TestRepositoryInterface */
    private $testRepository;

    /**@var ResultService */
    private $resultService;

    /**@var AnswerService */
    private $answerService;

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
        $this->resultService = self::$container->get(ResultService::class);
        $this->answerService = self::$container->get(AnswerService::class);
        $this->serializer = self::$container->get(AnswersSerializer::class);
        $this->test = $this->testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }
//
//    public function testViewStatusFinished()
//    {
//        $result = $this->initResult();
//        $this->resultRepository->save($result);
//        $categorySlug = $this->test->getCatalog()->getSlug();
//        $testSlug = $this->test->getSlug();
//        $this->client->request('POST', "/tests/$categorySlug/$testSlug/");
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//        dd($this->client->getResponse()->getContent());
//    }

    public function testViewPayableTestWithoutAccess()
    {
        $this->requestView();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//        $crawler = new Crawler($this->client->getResponse()->getContent());
//        $this->assertEquals(1, $crawler->filter('.start-test')->count());
//        $this->assertEquals(0, $crawler->filter('.restore-test')->count());
    }

    public function testViewStatusNone()
    {
        $categorySlug = $this->test->getCatalog()->getSlug();
        $testSlug = $this->test->getSlug();
        $this->client->request('POST', "/tests/$categorySlug/$testSlug/");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = new Crawler($this->client->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('.start-test')->count());
        $this->assertEquals(0, $crawler->filter('.restore-test')->count());
    }

    public function testViewStatusProcess()
    {
        $this->answerService->save($this->test, new Answer(1, ['some-value']));
        $categorySlug = $this->test->getCatalog()->getSlug();
        $testSlug = $this->test->getSlug();
        $this->client->request('POST', "/tests/$categorySlug/$testSlug/");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = new Crawler($this->client->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('.start-test')->count());
        $this->assertEquals(1, $crawler->filter('.restore-test')->count());
    }

    public function testViewStatusFinished()
    {
        $result = $this->initResult();
        $this->resultService->save($result);
        $this->resultService->saveSessionResult($result);
        $categorySlug = $this->test->getCatalog()->getSlug();
        $testSlug = $this->test->getSlug();
        $this->client->request('POST', "/tests/$categorySlug/$testSlug/");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = new Crawler($this->client->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('.test__btn-again')->count());
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

    private function initResult()
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
        $categorySlug = $this->test->getCatalog()->getSlug();
        $testSlug = $this->test->getSlug();
        $this->client->request('POST', "/tests/$categorySlug/$testSlug/");
    }
}