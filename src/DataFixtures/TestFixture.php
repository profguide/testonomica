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
    const TEST_4 = 'test_4';
    const TEST_5 = 'test_5'; // complex

    const TEST_1_SLUG = 'test_1';
    const TEST_2_SLUG = 'test_2';
    const TEST_3_SLUG = 'test_3';
    const TEST_4_SLUG = 'test_4';
    const TEST_5_SLUG = 'test_5';

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
        $test->setXmlFilename('proforientationTeen');
        $test->setCalculatorName('proforientationTeen');
        $manager->persist($test);
        $manager->flush();

        $this->addReference(self::TEST_2, $test);

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Тест на профориентацию для взрослых');
        $test->setNameEn('Proforientation test adult');
        $test->setSlug(self::TEST_3_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(60);
        $test->setXmlFilename('proforientationAdult');
        $test->setCalculatorName('proforientationAdult');
        $manager->persist($test);
        $manager->flush();

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Личность и структура интеллекта');
        $test->setNameEn('Personality and structure of intelligence');
        $test->setSlug(self::TEST_4_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(45);
        $test->setXmlFilename('personIntel');
        $test->setCalculatorName('personIntel');
        $manager->persist($test);
        $manager->flush();

        $this->addReference(self::TEST_4, $test);

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Комплексный тест');
        $test->setNameEn('Complex test');
        $test->setSlug(self::TEST_5_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(120);
        $test->setXmlFilename('testComplex');
        $test->setCalculatorName('testComplex');
        $manager->persist($test);
        $manager->flush();

        $this->addReference(self::TEST_5, $test);
    }

    public function getDependencies()
    {
        return [
            CategoryFixture::class
        ];
    }
}
