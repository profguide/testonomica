<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Test;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestFixture extends Fixture
{
    const TEST_1_SLUG = 'test_1';
    const TEST_2_SLUG = 'test_2';

    public function load(ObjectManager $manager)
    {
        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Тест 1');
        $test->setNameEn('Test 1');
        $test->setSlug(self::TEST_1_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(5);
        $test->setXmlFilename('test'); // << %kernel.project_dir%/xml/test.xml
        $test->setCalculatorName('test'); // << \App\Test\Calculator\TestCalculator
        $manager->persist($test);

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Тест на профориентацию №2');
        $test->setNameEn('Proforientation test #2');
        $test->setSlug(self::TEST_2_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(60);
        $test->setXmlFilename('proforientation2'); // << %kernel.project_dir%/xml/test.xml
        $test->setCalculatorName('proforientation2'); // \App\Test\Calculator\Proforientation2Calculator
        $manager->persist($test);

        $manager->flush();
    }
}
