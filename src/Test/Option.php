<?php

namespace App\Test;

/**
 * @author: adavydov
 * @since: 25.10.2020
 */
class Option
{
    private $value;

    private $correct;

    private $text;

    public function __construct($value, bool $correct, $text)
    {
        $this->value = $value;
        $this->correct = $correct;
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->correct;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }
}