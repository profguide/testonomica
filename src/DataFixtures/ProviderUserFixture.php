<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Provider;
use App\Entity\ProviderUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ProviderUserFixture extends Fixture implements DependentFixtureInterface
{
    public const PROFGUIDE_USER_1 = 'profguide_user_1';

    public function load(ObjectManager $manager): void
    {
        /**@var Provider $provider */
        $provider = $this->getReference(ProviderFixture::PROFGUIDE);

        $user = ProviderUser::create($provider, '100');
        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::PROFGUIDE_USER_1, $user);
    }

    public function getDependencies(): array
    {
        return [
            ProviderFixture::class
        ];
    }
}