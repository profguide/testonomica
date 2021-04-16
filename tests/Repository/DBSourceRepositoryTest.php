<?php

namespace App\Tests\Repository;

use App\DataFixtures\TestFixture;
use App\Entity\Test;
use App\Repository\DBSourceRepository;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DBSourceRepositoryTest extends KernelTestCase
{
    /**@var DBSourceRepository */
    private $dbSourceRepository;

    /**@var Test */
    private $test;

    public function setUp(): void
    {
        self::bootKernel();
        $testRepository = self::$container->get(TestRepository::class);
        $this->test = $testRepository->findOneBySlug(TestFixture::TEST_2_SLUG);
        $this->dbSourceRepository = self::$container->get(DBSourceRepository::class);
    }

    public function testFindQuestion()
    {
        $question = $this->dbSourceRepository->getQuestion($this->test, 1);
        $this->assertNotNull($question);
    }

    public function testNextQuestion()
    {
        $question = $this->dbSourceRepository->getNextQuestion($this->test, 1);
        $this->assertNotNull($question);
        $this->assertEquals(2, $question->getId());
    }

    public function testPrevQuestion()
    {
        $question = $this->dbSourceRepository->getPrevQuestion($this->test, 3);
        $this->assertNotNull($question);
        $this->assertEquals(2, $question->getId());
    }

    public function testFirstQuestion()
    {
        $question = $this->dbSourceRepository->getFirstQuestion($this->test);
        $this->assertNotNull($question);
        $this->assertEquals(1, $question->getId());
    }

    public function testLastQuestion()
    {
        $question = $this->dbSourceRepository->getLastQuestion($this->test);
        $this->assertNotNull($question);
        $this->assertEquals(3, $question->getId());
    }

    public function testQuestionNumber()
    {
        $this->assertEquals(1, $this->dbSourceRepository->getQuestionNumber($this->test, 1));
        $this->assertEquals(3, $this->dbSourceRepository->getQuestionNumber($this->test, 3));
    }

    public function testAllQuestions()
    {
        $questions = $this->dbSourceRepository->getAllQuestions($this->test);
        $this->assertCount(3, $questions);
    }

    public function testTotalCount()
    {
        $this->assertEquals(3, $this->dbSourceRepository->getTotalCount($this->test));
    }
}
