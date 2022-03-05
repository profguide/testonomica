<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleRequestListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

        $request = $event->getRequest();

        // add to nginx config:
        // fastcgi_param lang "en";
        $langServerParam = $_SERVER['lang'];
        $request->setLocale($langServerParam);
    }
}