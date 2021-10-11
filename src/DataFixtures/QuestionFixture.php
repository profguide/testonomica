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
    public function load(ObjectManager $manager)
    {
        /**@var Test $test */
        $test = $this->getReference(TestFixture::TEST_2);

        $question = new Question();
        $question->setTest($test);
        $question->setName("Какая планета находится дальше от Солнца?");
        $question->addItem(QuestionItem::createMinimal(0, "Земля", null, false));
        $question->addItem(QuestionItem::createMinimal(1, "Марс", null, true));
        $manager->persist($question);

        $question = new Question();
        $question->setTest($test);
        $question->setName("Какой объект больше?");
        $question->addItem(QuestionItem::createMinimal(1, "Солнце", null, true));
        $question->addItem(QuestionItem::createMinimal(0, "Луна", null, false));
        $manager->persist($question);

        $question = new Question();
        $question->setTest($test);
        $question->setName("Сколько планет в солнечной системе?");
        $question->addItem(QuestionItem::createMinimal(1, "8", null, true));
        $question->addItem(QuestionItem::createMinimal(0, "9", null, false));
        $manager->persist($question);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TestFixture::class
        ];
    }
}
