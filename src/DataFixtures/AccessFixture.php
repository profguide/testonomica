<?php
/**
 * @author: adavydov
 * @since: 15.11.2020
 */

namespace App\DataFixtures;

use App\Entity\Access;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AccessFixture extends Fixture
{
    const TOKEN = '92f50432-dd78-4f57-b407-6bb273c78f61';

    public function load(ObjectManager $manager)
    {
        $access = new Access();
        $access->setToken(self::TOKEN);
        $manager->persist($access);
        $manager->flush();
    }
}