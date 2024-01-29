<?php

declare(strict_types=1);

namespace App\Test\Proforientation;

final class TypesCombination
{
    const ALL = ['natural', 'tech', 'human', 'body', 'math', 'it', 'craft', 'art', 'hoz', 'com', 'boss', 'war'];

    const SUB = [
        'viz' => 'art',
        'muz' => 'art'
    ];

    /**
     * @var string[]
     */
    private array $values = [];

    public function __construct(array $types)
    {
        $this->values = $types;
    }

    public function values(): array
    {
        return $this->values;
    }
}