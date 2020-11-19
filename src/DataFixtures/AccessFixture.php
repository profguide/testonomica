<?php
/**
 * @author: adavydov
 * @since: 15.11.2020
 */

namespace App\DataFixtures;

use App\Entity\Access;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AccessFixture extends Fixture implements DependentFixtureInterface
{
    const TOKEN = '92f50432-dd78-4f57-b407-6bb273c78f61';

    public function load(ObjectManager $manager)
    {
        /**@var Service $service */
        $service = $this->getReference(ServiceFixture::SERVICE_1);

        $access = new Access();
        $access->setToken(self::TOKEN);
        $access->setService($service);
        $manager->persist($access);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ServiceFixture::class
        ];
    }
}