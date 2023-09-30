<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Transforms errors to JSON format for JSON requests.
 * Если в коннтроллере указать формат (format="json"), то
 * ошибка будет в json, но к сожалениею почему-то без подробностей.
 * Здесь ошибка наделяется подробностями.
 */
class ExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        // Теперь ясно почему у меня логи всегда были в json, да ещё и без стектрейса!!!!!!
        // сколько лет я мучился и как бы не настраивал монолог - всё было без толку...
//        $event->getRequest()->getAcceptableContentTypes();
//        if ($event->getRequest()->getContentType() === 'json') {
//            $event->setResponse(new JsonResponse([
//                'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
//                'title' => 'An error occurred',
//                'status' => $event->getThrowable()->getStatusCode(),
//                'detail' => $event->getThrowable()->getMessage()
//            ]));
//        }
    }
}