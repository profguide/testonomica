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
use App\Test\TestItemNotFoundException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SourceServiceTest extends KernelTestCase
{
    /**@var TestSourceService */
    private $service;

    /**@var Test */
    private $test;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->service = self::getContainer()->get(TestSourceService::class);
        $testRepository = self::getContainer()->get(TestRepositoryInterface::class);
        $this->test = $testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    /**
     * Exactly
     * Expect next question
     */
    public function testQuestion()
    {
        $id = 12;
        $question = $this->service->getQuestion($this->test, $id);
        $this->assertEquals($question->getId(), $id);
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
        $this->expectException(TestItemNotFoundException::class);
        $this->service->getPrevQuestion($this->test, $nonExistedQuestionId);
    }

    /**
     * Предыдуший вопрос от второго
     * Ожидание:
     * - вопрос должен быть найден
     */
    public function testGetPrevQuestion()
    {
        $question = $this->service->getPrevQuestion($this->test, 2);
        $this->assertEquals(1, $question->getId());
    }

    /**
     * Предыдуший вопрос от первого
     * Ожидание:
     * - вопрос НЕ должен быть найден
     */
    public function testGetPrevQuestionFromFirst()
    {
        $question = $this->service->getPrevQuestion($this->test, 1);
        $this->assertNull($question);
    }

    /**
     * Предыдуший вопрос от несуществующего
     * Ожидание:
     *  - исключение \App\Test\TestItemNotFoundException
     */
    public function testGetPrevQuestionFromNonExisted()
    {
        $nonQuestionIdExisted = -1;
        $this->expectException(TestItemNotFoundException::class);
        $this->service->getPrevQuestion($this->test, $nonQuestionIdExisted);
    }

    /*
     * First
     * Expect the first Question
     */
    public function testGetFirstQuestion()
    {
        $question = $this->service->getFirstQuestion($this->test);
        $this->assertEquals(1, $question->getId());
    }

    /*
     * Last
     * Expect the last Question
     */
    public function testGetLastQuestion()
    {
        $question = $this->service->getLastQuestion($this->test);
        $this->assertEquals(12, $question->getId());
    }

    /*
     * Total count
     * Expect correct count int
     */
    public function testGetTotalCount()
    {
        $this->assertEquals(12, $this->service->getTotalCount($this->test));
    }

    /*
     * Question position in the list
     * Expect correct int
     */
    public function testGetQuestionNumber()
    {
        $this->assertEquals(5, $this->service->getQuestionNumber($this->test, 5));
    }
}