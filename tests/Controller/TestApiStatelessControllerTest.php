<?php
/**
 * @author: adavydov
 * @since: 19.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\TestFixture;
use App\Entity\Answer;
use App\Entity\Result;
use App\Entity\Test;
use App\Repository\AnswerRepository;
use App\Repository\ResultRepository;
use App\Repository\TestRepositoryInterface;
use App\Service\ResultService;
use App\Test\AnswersSerializer;
use App\Test\TestStatus;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;


/**
 * Тестрируем stateless API прохождения теста, промежуточные данные хранятся у клиента (браузер, сервисы)
 * Class TestApiStatefulControllerTest
 * @package App\Tests\Controller
 */
class TestApiStatelessControllerTest extends WebTestCase
{
    /**@var TestRepositoryInterface */
    private $testRepository;

    /**@var AnswerRepository */
    private $answerRepository;

    /**@var ResultRepository */
    private $resultRepository;

    /**@var ResultService */
    private $resultService;

    /**@var AnswersSerializer */
    private $serializer;
//
    /**@var KernelBrowser */
    private $client;

    /**@var Test */
    private $test;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->testRepository = self::$container->get(TestRepositoryInterface::class);
        $this->answerRepository = self::$container->get(AnswerRepository::class);
        $this->resultRepository = self::$container->get(ResultRepository::class);
        $this->resultService = self::$container->get(ResultService::class);
        $this->serializer = self::$container->get(AnswersSerializer::class);
        $this->test = $this->testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    /*
     * Next
     * Ожидаем следующий вопрос
     */
    public function testNext()
    {
        $testId = $this->test->getId();
        $currentQuestionId = 1;
        $nextQuestionId = 2;
        $this->request(['test' => $testId, 'question' => $currentQuestionId, 'answer' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertHtml($testId, $nextQuestionId);
    }

    /*
     * Next последний
     * Ожидаем
     * - возврат заголовков и текст
     */
    public function testNextEnd()
    {
        $testId = $this->test->getId();
        $currentQuestionId = 12;
        $this->request(['test' => $testId, 'question' => $currentQuestionId, 'answer' => 'my-answer']);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("Обработка результата", $this->client->getResponse()->getContent());
        $this->assertEquals('test-status', $this->client->getResponse()->headers->get('access-control-expose-headers'));
        $this->assertEquals(TestStatus::FINISHED, $this->client->getResponse()->headers->get('test-status'));
    }

    /**
     * Back
     * Ожидаем предыдущий вопрос
     */
    public function testBack()
    {
        $testId = $this->test->getId();
        $currentQuestionId = 2;
        $prevQuestionId = 1;
        $this->request(['test' => $testId, 'question' => $currentQuestionId, 'back' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertHtml($testId, $prevQuestionId);
    }

    public function testSaveResults()
    {
        $dataSource = '{"1":{"questionId":"1","value":"my-answer"}}';
        $this->requestSave($this->test, $dataSource);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $uuid = $this->client->getResponse()->getContent();
        $this->assertNotNull($uuid);
        $result = $this->resultRepository->findByUuid($uuid);
        $this->assertNotNull($result);
        $this->assertEquals($dataSource, $result->getData(), 'Данные не исказились');
    }

    private function assertHtml($testId, $nextQuestionId)
    {
        $crawler = new Crawler($this->client->getResponse()->getContent());
        $this->assertEquals($testId, $crawler->filter('input[name="test"]')->attr('value'));
        $this->assertEquals($nextQuestionId, $crawler->filter('input[name="question"]')->attr('value'));
    }

    private function request(array $params)
    {
        $this->client->request('POST', '/tests/cli/', $params);
    }

    private function requestSave(Test $test, string $answers)
    {
        $this->client->request('POST', "/tests/cli/save/{$test->getId()}/", ['answers' => $answers]);
    }
}