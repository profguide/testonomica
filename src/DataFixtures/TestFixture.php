<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Test;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TestFixture extends Fixture implements DependentFixtureInterface
{
    const TEST_1 = 'test_1';
    const TEST_2 = 'test_2';
    const TEST_3 = 'test_3';

    const TEST_1_SLUG = 'test_1';
    const TEST_2_SLUG = 'test_2';
    const TEST_3_SLUG = 'test_3';

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
        $manager->flush();

        $this->addReference(self::TEST_1, $test);

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Тест на профориентацию для подростков');
        $test->setNameEn('Proforientation child');
        $test->setSlug(self::TEST_2_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(60);
        $test->setXmlFilename('proforientationTeen'); // << %kernel.project_dir%/xml/test.xml
        $test->setCalculatorName('proforientationTeen'); // \App\Test\Calculator\ProforientationTeenCalculator
        $manager->persist($test);
        $manager->flush();

        $this->addReference(self::TEST_2, $test);

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Тест на профориентацию для взрослых');
        $test->setNameEn('Proforientation test adult');
        $test->setSlug(self::TEST_2_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(60);
        $test->setXmlFilename('proforientationAdult'); // << %kernel.project_dir%/xml/test.xml
        $test->setCalculatorName('proforientationAdult'); // \App\Test\Calculator\ProforientationAdultCalculator
        $manager->persist($test);
        $manager->flush();

        $this->addReference(self::TEST_3, $test);
    }

    public function getDependencies()
    {
        return [
            CategoryFixture::class
        ];
    }
}
