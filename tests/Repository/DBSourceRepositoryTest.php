<?php

namespace App\Tests\Repository;

use App\DataFixtures\TestFixture;
use App\Entity\Test;
use App\Repository\DBSourceRepository;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DBSourceRepositoryTest extends KernelTestCase
{
    /**@var TestRepository */
    private $testRepository;

    /**@var DBSourceRepository */
    private $dbSourceRepository;

    /**@var Test */
    private $test;

    public function setUp()
    {
        self::bootKernel();
        $this->testRepository = self::$container->get(TestRepository::class);
        $this->test = $this->testRepository->findOneBySlug(TestFixture::TEST_2_SLUG);
//        $test = TestFixture::;
        $this->dbSourceRepository = self::$container->get(DBSourceRepository::class);
    }

    /**
     *
     */
    public function testFindQuestion()
    {
        $this->dbSourceRepository->getQuestion();
    }
}
