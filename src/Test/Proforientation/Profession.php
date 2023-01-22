<?php
/**
 * @author: adavydov
 * @since: 06.11.2020
 */

namespace App\Test\Proforientation;

use App\Tests\Test\Proforientation\ProfessionTest;
use JsonSerializable;

/**
 * @see ProfessionTest
 */
class Profession implements JsonSerializable
{
    private string $name;

    private Types $types;

    private TypesCombination $typesNot;

    private array $description;

    private ValueSystem $systemValues;

    private int $rating = 0;

    private float $valueScore = 0;

    public function __construct(string $name, Types $types, TypesCombination $typesNot, ValueSystem $valueSystem, $description = [])
    {
        $this->name = $name;
        $this->types = $types;
        $this->typesNot = $typesNot;
        $this->description = $description;
        $this->systemValues = $valueSystem;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function types(): Types
    {
        return $this->types;
    }

    public function typesNot(): TypesCombination
    {
        return $this->typesNot;
    }

    public function valueSystem(): ValueSystem
    {
        return $this->systemValues;
    }

    /**
     * todo add ProfessionScore: {values, types}
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * todo add ProfessionScore: {values, types}
     * @param int $rating
     */
    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return array
     */
    public function description(): array
    {
        return $this->description;
    }

    // todo add ProfessionScore: {values, types}
    public function setValueScore(float $score)
    {
        $this->valueScore = $score;
    }

    public function getValueScore(): float
    {
        return $this->valueScore;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}