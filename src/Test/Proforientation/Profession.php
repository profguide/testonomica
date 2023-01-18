<?php
/**
 * @author: adavydov
 * @since: 06.11.2020
 */

namespace App\Test\Proforientation;

use App\Tests\Test\Proforientation\ProfessionTest;
use InvalidArgumentException;

/**
 * @see ProfessionTest
 */
class Profession
{
    private string $name;

    private array $combs;

    private array $not;

    private array $description;

    private ValueSystem $systemValues;

    private int $rating = 0;

    public function __construct(string $name, array $combs, ValueSystem $valueSystem, $not = [], $description = [])
    {
        self::guardCombs($combs);

        $this->name = $name;
        $this->combs = $combs;
        $this->not = $not;
        $this->description = $description;
        $this->systemValues = $valueSystem;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCombs(): array
    {
        return $this->combs;
    }

    public function getNot(): array
    {
        return $this->not;
    }

    public function valueSystem(): ValueSystem
    {
        return $this->systemValues;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return array
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    private static function guardCombs(array $combs)
    {
        foreach ($combs as $comb) {
            if (!is_array($comb)) {
                throw new InvalidArgumentException('Every combination must be an array.');
            }
        }
    }
}