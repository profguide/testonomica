<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Result;
use App\Entity\Test;
use App\Test\Progress\Progress;
use App\Test\Progress\ProgressSerializer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\UuidV4;

final class ResultFixture extends Fixture implements DependentFixtureInterface
{
    public const PROFTEST_1 = 'result_proftest';

    public function load(ObjectManager $manager): void
    {
        /**@var Test $test */
        $test = $this->getReference(TestFixture::TEST_3);

        $result = Result::createAutoKey($test, new Progress([new Answer('1', ['a', 'b'])]), new ProgressSerializer());
        $manager->persist($result);
        $manager->flush();

        $this->addReference(self::PROFTEST_1, $result);
    }

    public function getDependencies(): array
    {
        return [
            TestFixture::class
        ];
    }
}