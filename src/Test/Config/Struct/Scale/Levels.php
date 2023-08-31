<?php

declare(strict_types=1);

namespace App\Test\Config\Struct\Scale;

final readonly class Levels
{
    /**
     * @param Level[] $levels
     */
    public function __construct(public array $levels)
    {
        self::guardLevels($this->levels);
    }

    /**
     * @param Level[] $levels
     * @return void
     */
    private static function guardLevels(array $levels): void
    {
        $max = null;
        foreach ($levels as $level) {
            if ($level->upTo === 100) {
                $max = $level;
            }
        }

        if ($max === null) {
            throw new \DomainException("100% level has to be present.");
        }
    }
}