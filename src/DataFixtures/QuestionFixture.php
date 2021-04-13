<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\QuestionItem;
use App\Entity\Test;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class QuestionFixture extends Fixture implements DependentFixtureInterface
{
    const QUESTION_1 = 'question_1';
    const QUESTION_2 = 'question_2';

    public function load(ObjectManager $manager)
    {
        /**@var Test $test */
        $test = $this->getReference(TestFixture::TEST_2);

        $question = new Question();
        $question->setTest($test);
        $question->setName("Какая планета находится дальше от Солнца?");
        $question->addItem(QuestionItem::createMinimal(0, "Земля", false));
        $question->addItem(QuestionItem::createMinimal(1, "Марс", true));
        $manager->persist($question);
        $this->addReference(self::QUESTION_1, $question);

        $question = new Question();
        $question->setTest($test);
        $question->setName("Какой объект больше?");
        $question->addItem(QuestionItem::createMinimal(1, "Солнце", true));
        $question->addItem(QuestionItem::createMinimal(0, "Луна", false));
        $manager->persist($question);
        $this->addReference(self::QUESTION_2, $question);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TestFixture::class
        ];
    }
}
