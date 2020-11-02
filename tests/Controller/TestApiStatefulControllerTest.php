<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\TestFixture;
use App\Entity\Answer;
use App\Entity\Test;
use App\Repository\AnswerRepository;
use App\Repository\TestRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Тестрируем публичное API с сохранением состояния
 * Class TestApiStatefulControllerTest
 * @package App\Tests\Controller
 */
class TestApiStatefulControllerTest extends WebTestCase
{
    /**@var TestRepositoryInterface */
    private $testRepository;

    /**@var AnswerRepository */
    private $answerRepository;

    /**@var KernelBrowser */
    private $client;

    /**@var Test */
    private $test;

    /**@var SessionInterface */
    private $session;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->testRepository = self::$container->get(TestRepositoryInterface::class);
        $this->answerRepository = self::$container->get(AnswerRepository::class);
        $this->session = self::$container->get('session');
        $this->test = $this->testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    /*
     * Next
     * Ожидаем следующий вопрос и сохранение в репозитории Answer
     */
    public function testApiNext()
    {
        $testId = $this->test->getId();
        $currentQuestionId = 1;
        $nextQuestionId = 2;
        $this->client->request('POST', '/tests/api/', ['test' => $testId, 'question' => $currentQuestionId, 'answer' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // ответ сохранён
        $this->assertEquals(1, $this->answerRepository->count($this->test));
        // вернулся следующий вопрос
        $this->assertForm($testId, $nextQuestionId);
    }

    // todo next end

    /**
     * Back
     * Ожидаем предыдущий вопрос
     */
    public function testApiBack()
    {
        $testId = $this->test->getId();
        $currentQuestionId = 2;
        $prevQuestionId = 1;
        $this->client->request('POST', '/tests/api/', ['test' => $testId, 'question' => $currentQuestionId, 'back' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // вернулся предыдущий вопрос
        $this->assertForm($testId, $prevQuestionId);
    }

    /**
     * Clear
     * Ожидаем первый вопрос
     */
    public function testApiClear()
    {
        $testId = $this->test->getId();
        $firstQuestionId = 1;
        $this->client->request('POST', '/tests/api/', ['test' => $testId, 'clear' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // вернулся предыдущий вопрос
        $this->assertForm($testId, $firstQuestionId);
    }

    /**
     * Restore
     * Ожидаем следующий вопрос после вопроса, который последний в прогрессе
     */
    public function testApiRestore()
    {
        $testId = $this->test->getId();
        $lastQuestionId = 2;
        $nextQuestionId = 3;
        $this->answerRepository->save($this->test, Answer::create($lastQuestionId, 'some-value'));
        $this->client->request('POST', '/tests/api/', ['test' => $testId, 'restore' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // следующий вопрос
        $this->assertForm($testId, $nextQuestionId);
    }

    private function assertForm($testId, $nextQuestionId)
    {
        $crawler = new Crawler($this->client->getResponse()->getContent());
        $this->assertEquals($testId, $crawler->filter('input[name="test"]')->attr('value'));
        $this->assertEquals($nextQuestionId, $crawler->filter('input[name="question"]')->attr('value'));
    }
}