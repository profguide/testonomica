<?php

namespace App\Test;

/**
 * @author: adavydov
 * @since: 25.10.2020
 */
class Field
{
    private $type;

    private $placeholder;

    private $correct;

    public function __construct($type, $placeholder, $correct)
    {
        $this->type = $type; // todo assert type
        $this->placeholder = $placeholder;
        $this->correct = $correct;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * @return string
     */
    public function getCorrect(): string
    {
        return $this->correct;
    }
}