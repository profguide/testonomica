<?php
/**
 * @author: adavydov
 * @since: 15.11.2020
 */

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('david.tema@yandex.ru');
        $user->setPassword('$2y$13$l65hazE8QsxHMQzUzZ72tOo8zj.6d524Amzi1sEZGqd8AHwm7gRfK');
        $user->setRoles('["ROLE_ADMIN"]');
        $manager->persist($user);
        $manager->flush();
    }
}