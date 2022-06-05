<?php

declare(strict_types=1);

namespace App\Test;

class ViewFormat
{
    const PDF = 'pdf';
    const HTML = 'html';
    const JSON = 'json';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, ['json', 'html', 'pdf'])) {
            throw new \DomainException("Illegal format value $value. Must be json, html of pdf.");
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}