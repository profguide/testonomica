<?php

declare(strict_types=1);

namespace App\Subscriber;

use Symfony\Component\HttpFoundation\RequestStack;

// todo move to another namespace
class Locale
{
    const LOCALES = [
        'ru', 'en'
    ];

    private string $value;

    public function __construct(RequestStack $requestStack)
    {
        if (PHP_SAPI === 'cli') {
            $this->value = 'ru';
        } else {
            $this->value = $requestStack->getMainRequest()->getLocale();
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}