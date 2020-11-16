<?php
/**
 * @author: adavydov
 * @since: 15.11.2020
 */

namespace App\DataFixtures;

use App\Entity\Payment;
use App\Entity\Provider;
use App\Entity\ProviderAccess;
use App\Entity\ProviderPayment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProviderAccessFixture extends Fixture implements DependentFixtureInterface
{
    const TOKEN = '92f50432-dd78-4f57-b407-6bb273c78f61';

    public function load(ObjectManager $manager)
    {
        /**@var Provider $provider */
        $provider = $this->getReference(ProviderFixture::TESTOMETRIKA);

        $access = new ProviderAccess();
        $access->setProvider($provider);
        $access->setToken(self::TOKEN);

        $manager->persist($access);
        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array class-string[]
     */
    public function getDependencies()
    {
        return [
            ProviderFixture::class,
        ];
    }
}