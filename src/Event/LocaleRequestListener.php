<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleRequestListener
{
    private string $defaultLocale;

    public function __construct(string $defaultLocale = 'ru')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

        $request = $event->getRequest();
        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);
        } else {
            $request->setLocale($this->defaultLocale);
        }

//        if (!$event->isMainRequest()) {
//            // don't do anything if it's not the main request
//            return;
//        }
//
//        $request = $event->getRequest();
//
//        // add to nginx config:
//        // fastcgi_param lang "en";
//        $langServerParam = $_SERVER['lang'];
//        $request->setLocale($langServerParam);
    }
}