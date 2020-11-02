<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Tests\Service;


use App\DataFixtures\TestFixture;
use App\Entity\Result;
use App\Entity\Test;
use App\Repository\TestRepositoryInterface;
use App\Service\ResultService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResultServiceTest extends KernelTestCase
{
    /**@var ResultService */
    private $service;

    /**@var Test */
    private $test;

    public function setUp()
    {
        self::bootKernel();
        $this->service = self::$container->get(ResultService::class);
        $testRepository = self::$container->get(TestRepositoryInterface::class);
        $this->test = $testRepository->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    public function testSave()
    {
        $result = Result::create($this->test, '00000000-0000-0000-0000-000000000000', '');
        $savedResult = $this->service->save($result);
        $this->assertNotNull($this->service->findByUuid($savedResult->getUuid()));
    }
}