<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Provider;
use App\Entity\ProviderUserResult;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ProviderUserResultFixture extends Fixture implements DependentFixtureInterface
{
    public const PROFGUIDE_USER_1_RESULT_1 = 'profguide_user_1_result_1';

    public function load(ObjectManager $manager): void
    {
        /**@var Provider $provider */
        $test = $this->getReference(TestFixture::TEST_3);
        $user = $this->getReference(ProviderUserFixture::PROFGUIDE_USER_1);
        $result = $this->getReference(ResultFixture::PROFTEST_1);

        $userResult = ProviderUserResult::create($user, $result, $test);

        $manager->persist($userResult);
        $manager->flush();

        $this->addReference(self::PROFGUIDE_USER_1_RESULT_1, $userResult);
    }

    public function getDependencies(): array
    {
        return [
            ProviderFixture::class,
            ProviderUserFixture::class,
            ResultFixture::class
        ];
    }
}