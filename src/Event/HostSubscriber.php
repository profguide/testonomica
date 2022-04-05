<?php

declare(strict_types=1);

namespace App\Event;

use App\Controller\HostAuthenticatedController;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @see HostAuthenticatedController
 */
class HostSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
            $this->logger->warning("HostSubscriber", ['host' => $host]);
            if (!in_array($host, [
                'profguide.io',
                'www.profguide.io',
                'pg',
                'chooseyourcareer.ru',
                'career.local',
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