<?php

declare(strict_types=1);

namespace App\Event;

use App\Controller\HostAuthenticatedController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class HostSubscriber implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof HostAuthenticatedController) {
//            if ($event->getRequest()->server->get('SERVER_NAME') === '127.0.0.1:8000') {
//                return;
//            }
            $host = parse_url($event->getRequest()->server->get('HTTP_REFERER'), PHP_URL_HOST);
            if (!in_array($host, [
                'profguide.io',
                'www.profguide.io',
                'http://pg/',
                'https://www.profguide.io/',
                'https://chooseyourcareer.ru/',
                'http://career.local/',
                'https://career.local/'
            ])) {
                throw new AccessDeniedHttpException("Unknown host $host.");
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}