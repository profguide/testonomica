<?php
/**
 * @author: adavydov
 * @since: 30.10.2020
 */

namespace App\Entity;


class Answer
{
    public $questionId;
    public $value;

    private function __construct(string $id, string $value)
    {
        $this->questionId = $id;
        $this->value = $value;
    }

    public static function create(string $id, string $value)
    {
        return new static($id, $value);
    }

    public function getQuestionId(): string
    {
        return $this->questionId;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}