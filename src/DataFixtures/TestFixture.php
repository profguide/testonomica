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
    const TEST_5 = 'test_5';
    const TEST_6 = 'test_6'; // complex

    const TEST_1_SLUG = 'test_1'; // xml-based
    const TEST_2_SLUG = 'test_2'; // db-based
    const TEST_3_SLUG = 'test_3'; // профтест
    const TEST_5_SLUG = 'test_5'; // личность и структура
    const TEST_6_SLUG = 'test_6'; // complex

    public function load(ObjectManager $manager)
    {
        // XML-based
        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Тест 1 XML-based');
        $test->setNameEn('Test 1 XML-based');
        $test->setSlug(self::TEST_1_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(5);
        $test->setIsXmlSource(true); // <<
        $test->setSourceName('testTest');
        $manager->persist($test);
        $manager->flush();
        $this->addReference(self::TEST_1, $test);

        // DB-based
        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Тест 2 DB-based');
        $test->setNameEn('Test 2 DB-based');
        $test->setSlug(self::TEST_2_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(5);
        $test->setIsXmlSource(false); // <<
        $manager->persist($test);
        $manager->flush();
        $this->addReference(self::TEST_2, $test);

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Профтест');
        $test->setNameEn('Career Guidance Test');
        $test->setSlug(self::TEST_3_SLUG);
        $test->setDescription('Профтест разработан в центре профориентации ПрофГид');
        $test->setAnnotation('Career Guidance Test has been developed at the career guidance center ProfGuide.');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(30);
        $test->setIsXmlSource(true);
        $test->setSourceName('proftest');
        $manager->persist($test);
        $manager->flush();

        $this->addReference(self::TEST_3, $test);

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Личность и структура интеллекта');
        $test->setNameEn('Personality and structure of intelligence');
        $test->setSlug(self::TEST_5_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(45);
        $test->setIsXmlSource(true);
        $test->setSourceName('personIntel');
        $manager->persist($test);
        $manager->flush();

        $this->addReference(self::TEST_5, $test);

        /**@var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_PSYCHOLOGICAL_REFERENCE);
        $test = new Test();
        $test->setCatalog($category);
        $test->setName('Комплексный тест');
        $test->setNameEn('Complex test');
        $test->setSlug(self::TEST_6_SLUG);
        $test->setDescription('Some description');
        $test->setAnnotation('Some annotation');
        $test->setActive(1);
        $test->setActiveEn(1);
        $test->setDuration(120);
        $test->setIsXmlSource(true);
//        $test->setXmlFilename('testComplex');
//        $test->setCalculatorName('testComplex');
        $test->setSourceName('testComplex');
        $manager->persist($test);
        $manager->flush();

        $this->addReference(self::TEST_6, $test);
    }

    public function getDependencies()
    {
        return [
            CategoryFixture::class
        ];
    }
}
