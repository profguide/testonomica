<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Tests\Service;


use App\DataFixtures\TestFixture;
use App\Entity\Answer;
use App\Entity\Result;
use App\Entity\Test;
use App\Repository\TestRepositoryInterface;
use App\Service\ResultService;
use App\Test\Progress\Progress;
use App\Test\Progress\ProgressSerializer;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResultServiceTest extends KernelTestCase
{
    /**@var ResultService */
    private $service;

    private ?ProgressSerializer $serializer;

    /**@var Test */
    private $test;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->service = self::getContainer()->get(ResultService::class);
        $testRepository = self::getContainer()->get(TestRepositoryInterface::class);
        $this->serializer = static::getContainer()->get(ProgressSerializer::class);
        $this->test = $testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    public function testSave()
    {
        $progress = new Progress([new Answer('1', ['a', 'b'])]);
        $result = Result::createAutoKey($this->test, $progress, $this->serializer);
        $savedResult = $this->service->save($result);
        $this->assertNotNull($this->service->findByUuid($savedResult->getUuid()));
    }
}