<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Test;


class AnswersHolder
{
    private $answers;

    public function __construct(array $answers)
    {
        $this->answers = $answers;
    }

    public function getAll(): array
    {
        return $this->answers;
    }
}