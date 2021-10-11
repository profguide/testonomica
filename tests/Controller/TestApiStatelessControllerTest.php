<?php
/**
 * @author: adavydov
 * @since: 19.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\TestFixture;
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

    /**@var Test */
    private $complexTest;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->testRepository = self::getContainer()->get(TestRepositoryInterface::class);
        $this->answerRepository = self::getContainer()->get(AnswerRepository::class);
        $this->resultRepository = self::getContainer()->get(ResultRepository::class);
        $this->resultService = self::getContainer()->get(ResultService::class);
        $this->serializer = self::getContainer()->get(AnswersSerializer::class);
        $this->test = $this->testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
        $this->complexTest = $this->testRepository->findOneBySlug(TestFixture::TEST_6_SLUG);
    }

    /**
     * Start
     * Загрузка первого вопроса
     * Ожидаем первый вопрос
     */
    public function testFirst()
    {
        $testId = $this->test->getId();
        $nextQuestionId = 1;
        $this->request(['test' => $testId, 'start' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertHtml($testId, $nextQuestionId);
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
        $this->assertEquals('Test-Status', $this->client->getResponse()->headers->get('Access-Control-Expose-Headers'));
        $this->assertEquals(TestStatus::FINISHED, $this->client->getResponse()->headers->get('Test-Status'));
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
        $dataSource = '{"1":["my-answer"]}';
        $this->requestSave($this->test, $dataSource);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $uuid = $this->client->getResponse()->getContent();
        $this->assertNotNull($uuid);
        $result = $this->resultRepository->findByUuid($uuid);
        $this->assertNotNull($result);
        $this->assertEquals($dataSource, $result->getData(), 'Данные не исказились');
    }

    public function testCalculateSingleResult()
    {
        $testId = $this->test->getId();
        $dataSource = '{"101":["my-answer"]}';
        $dataExpect = '{"101":{"questionId":"101","value":["my-answer"]}}';
        $this->client->request('POST', "/tests/cli/calculate/{$testId}/", ['result' => $dataSource]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($dataExpect, $this->client->getResponse()->getContent());
    }

    public function testCalculateComplexResult()
    {
        $partialTestId = $this->test->getId();
        // {"12":{"1":["my-answer"]}}
        $dataSource = json_encode([
            $partialTestId => [
                "101" => ["my-answer"]
            ]
        ]);
        // {"12":{"1":{"questionId":"1","value":["my-answer"]}}}
        $dataExpect = json_encode([
            $partialTestId => [
                "101" => [
                    'questionId' => "101",
                    'value' => ["my-answer"]
                ]
            ]
        ]);
        $complexTestId = $this->complexTest->getId();
        $this->client->request('POST', "/tests/cli/calculate/{$complexTestId}/", ['result' => $dataSource]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($dataExpect, $this->client->getResponse()->getContent());
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
        $this->client->request('POST', "/tests/cli/save/{$test->getId()}/", ['result' => $answers]);
    }
}