<?php
/**
 * @author: adavydov
 * @since: 31.10.2020
 */

namespace App\Tests\Service;


use App\DataFixtures\TestFixture;
use App\Entity\Test;
use App\Repository\TestRepositoryInterface;
use App\Service\TestSourceService;
use App\Test\Question;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SourceServiceTest extends KernelTestCase
{
    /**@var TestSourceService */
    private $service;

    /**@var Test */
    private $test;

    public function setUp()
    {
        self::bootKernel();
        $this->service = self::$container->get(TestSourceService::class);
        $testRepository = self::$container->get(TestRepositoryInterface::class);
        $this->test = $testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    /**
     * Next
     * Expect next question
     */
    public function testGetNextQuestion()
    {
        $firstQuestionId = 1;
        $nextQuestionId = 2;
        $question = $this->service->getNextQuestion($this->test, $firstQuestionId);
        $this->assertEquals($question->getId(), $nextQuestionId);
    }

    /**
     * Next
     * From the last
     * Expect null
     */
    public function testGetNextQuestionFromLast()
    {
        $lastQuestionId = 12;
        $question = $this->service->getNextQuestion($this->test, $lastQuestionId);
        $this->assertNull($question);
    }

    /**
     * Next
     * From non-existed
     * Expect exception
     */
    public function testGetNextQuestionFromNonExisted()
    {
        $nonExistedQuestionId = -1;
        $this->expectException(\App\Test\TestItemNotFoundException::class);
        $this->service->getPrevQuestion($this->test, $nonExistedQuestionId);
    }

    /**
     * Back
     * Expect previous question
     */
    public function testGetPrevQuestion()
    {
        $currentQuestionId = 2;
        $firstQuestionId = 1;
        $question = $this->service->getPrevQuestion($this->test, $currentQuestionId);
        $this->assertEquals($question->getId(), $firstQuestionId);
    }

    /**
     * Back
     * First question id is passed
     * Expects null
     */
    public function testGetPrevQuestionFromFirst()
    {
        $firstQuestionId = 1;
        $question = $this->service->getPrevQuestion($this->test, $firstQuestionId);
        $this->assertNull($question);
    }

    /**
     * Back
     * Non-existed question id passed
     * Expects Exception
     */
    public function testGetPrevQuestionFromNonExisted()
    {
        $nonQuestionIdExisted = -1;
        $this->expectException(\App\Test\TestItemNotFoundException::class);
        $this->service->getPrevQuestion($this->test, $nonQuestionIdExisted);
    }

    /*
     * First
     * Expect the first Question
     */
    public function testGetFirstQuestion()
    {
        $firstQuestionId = 1;
        $question = $this->service->getFirstQuestion($this->test);
        $this->assertEquals($question->getId(), $firstQuestionId);
    }

    /*
     * Last
     * Expect the last Question
     */
    public function testGetLastQuestion()
    {
        $lastQuestionId = 12;
        $question = $this->service->getLastQuestion($this->test);
        $this->assertEquals($lastQuestionId, $question->getId());
    }

    /*
     * Total count
     * Expect correct count int
     */
    public function testGetTotalCount()
    {
        $count = 12;
        $this->assertEquals($count, $this->service->getTotalCount($this->test));
    }

    /*
     * Question position in the list
     * Expect correct int
     */
    public function testGetQuestionNumber()
    {
        $expectNumber = 5;
        $question = new Question();
        $question->setId(5);
        $this->assertEquals($expectNumber, $this->service->getQuestionNumber($this->test, $question));
    }
}