<?php
/**
 * @author: adavydov
 * @since: 18.11.2020
 */

namespace App\DataFixtures;

use App\Entity\Service;
use App\Entity\Test;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ServiceFixture extends Fixture implements DependentFixtureInterface
{
    public const SERVICE_1 = 'proftest';

    public function load(ObjectManager $manager)
    {
        /**@var Test $test */
        $test = $this->getReference(TestFixture::TEST_3);
        $service = new Service("Тест на профориентацию", 99, self::SERVICE_1, 'Тест на профориентацию');
        $service->addTest($test);
        $manager->persist($service);
        $manager->flush();

        $this->addReference(self::SERVICE_1, $service);
    }

    public function getDependencies()
    {
        return [
            TestFixture::class
        ];
    }
}