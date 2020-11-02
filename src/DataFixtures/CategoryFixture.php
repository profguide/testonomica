<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends Fixture
{
    public const CATEGORY_PSYCHOLOGICAL_REFERENCE = 'category_1';

    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Психологические');
        $category->setNameEn('Psychological');
        $category->setSlug('psychology');
        $category->setActive(1);
        $manager->persist($category);
        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::CATEGORY_PSYCHOLOGICAL_REFERENCE, $category);
    }
}
