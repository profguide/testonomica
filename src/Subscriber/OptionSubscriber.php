<?php


namespace App\Subscriber;


use App\Entity\QuestionItem;
use App\Entity\Question;
use App\Entity\Test;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => ['setTest']
        ];
    }

    public function setTest(BeforeEntityUpdatedEvent $event){
        if ($event->getEntityInstance() instanceof Test) {
            /**@var Test $test*/
            $test = $event->getEntityInstance();
            /**@var Question $question*/
            foreach ($test->getQuestions() as $question) {
                $question->setTest($test);
            }
//            dd($event);
        }
    }
}