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
    private $answerService;

    /**@var Test */
    private $test;

    /**@var SessionInterface */
    private $session;

    public function setUp()
    {
        self::bootKernel();
        $this->session = self::$container->get('session');
        $this->answerService = self::$container->get(AnswerService::class);
        $testRepository = self::$container->get(TestRepositoryInterface::class);
        $this->test = $testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    public function testSave()
    {
        $answer = Answer::create(99, 'my-value');
        $this->answerService->save($this->test, $answer);
        $this->assertEquals(1, $this->answerService->count($this->test));
        // repeated answer must rewrite value
        $this->answerService->save($this->test, $answer);
        $this->assertEquals(1, $this->answerService->count($this->test));
    }

    public function testClear()
    {
        $answer = Answer::create(99, 'my-value');
        $this->answerService->save($this->test, $answer);
        $this->assertEquals(1, $this->answerService->count($this->test));
        $this->answerService->clear($this->test);
        $this->assertEquals(0, $this->answerService->count($this->test));
    }

    public function testGetAll()
    {
        $answer = Answer::create(99, 'my-value');
        // save
        $this->answerService->save($this->test, $answer);
        // get
        $answers = $this->answerService->getAll($this->test);
        $this->assertCount(1, $answers);
        $this->assertEquals($answer, $answers[99]);
    }

    public function testHasAnswers()
    {
        $this->assertFalse($this->answerService->hasAnswers($this->test));
        $this->answerService->save($this->test, Answer::create(99, 'my-value'));
        $this->assertTrue($this->answerService->hasAnswers($this->test));
    }
}