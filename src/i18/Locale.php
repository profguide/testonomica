<?php

declare(strict_types=1);

namespace App\i18;

class Locale
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function value(): string
    {
        return $this->locale;
    }
}