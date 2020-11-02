<?php

namespace App\Tests\Controller;

use App\DataFixtures\TestFixture;
use App\Entity\Test;
use App\Repository\TestRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author: adavydov
 * @since: 24.10.2020
 */
class TestApiControllerTest extends WebTestCase
{
    /**@var TestRepositoryInterface */
    private $testRepository;

    /**@var KernelBrowser */
    private $client;

    /**@var Test */
    private $test;

    /**@var SessionInterface */
    private $session;

    protected function setUp()
    {
//        static::bootKernel();
        $this->client = static::createClient();
        $this->testRepository = self::$container->get(TestRepositoryInterface::class);
        $this->session = self::$container->get('session');
        $this->test = $this->testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    /*
     * Next
     * Ожидаем следующий вопрос
     */
    public function testApiNext()
    {
        $testId = $this->test->getId();
        $this->client->request('POST', '/tests/api/', ['test' => $testId, 'answer' => 1, 'question' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Back
     * Ожидаем предыдущий вопрос
     */
    public function testApiBack()
    {
        $testId = $this->test->getId();
        $this->client->request('POST', '/tests/api/', ['test' => $testId, 'question' => 2, 'back' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Clear
     * Ожидаем первый вопрос
     */
    public function testApiClear()
    {
        $testId = $this->test->getId();
        $this->client->request('POST', '/tests/api/', ['test' => $testId, 'clear' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Restore
     * Ожидаем следующий вопрос после того, что хранится в сессию пользователя
     */
    public function testApiRestore()
    {
        $testId = $this->test->getId();
        $this->session->set("test-{$testId}", "{тут типа массив значений}");

        $this->client->request('POST', '/tests/api/', ['test' => $testId, 'restore' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}