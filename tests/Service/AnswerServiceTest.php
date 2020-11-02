<?php
/**
 * @author: adavydov
 * @since: 31.10.2020
 */

namespace App\Tests\Service;


use App\DataFixtures\TestFixture;
use App\Entity\Answer;
use App\Entity\Test;
use App\Repository\TestRepositoryInterface;
use App\Service\AnswerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AnswerServiceTest extends KernelTestCase
{
    /**@var AnswerService */
    private $service;

    /**@var Test */
    private $test;

    /**@var SessionInterface */
    private $session;

    public function setUp()
    {
        self::bootKernel();
        $this->session = self::$container->get('session');
        $this->service = self::$container->get(AnswerService::class);
        $testRepository = self::$container->get(TestRepositoryInterface::class);
        $this->test = $testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    public function testSave()
    {
        $answer = Answer::create(99, 'my-value');
        $this->service->save($this->test, $answer);
        $this->assertEquals(1, $this->service->count($this->test));
        // repeated answer must rewrite value
        $this->service->save($this->test, $answer);
        $this->assertEquals(1, $this->service->count($this->test));
    }

    public function testClear()
    {
        $answer = Answer::create(99, 'my-value');
        $this->service->save($this->test, $answer);
        $this->assertEquals(1, $this->service->count($this->test));
        $this->service->clear($this->test);
        $this->assertEquals(0, $this->service->count($this->test));
    }

    public function testGetAll()
    {
        $answer = Answer::create(99, 'my-value');
        // save
        $this->service->save($this->test, $answer);
        // get
        $answers = $this->service->getAll($this->test);
        $this->assertCount(1, $answers);
        $this->assertEquals($answer, $answers[99]);
    }

//
//    public function testStatus()
//    {
//
//    }
}