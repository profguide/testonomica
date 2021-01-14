<?php
/**
 * @author: adavydov
 * @since: 30.10.2020
 */

namespace App\Entity;

class Answer
{
    public string $questionId;

    public array $value;

    private function __construct(string $id, array $value)
    {
        $this->questionId = $id;
        $this->value = $value;
    }

    public static function create(string $id, array $value): Answer
    {
        return new static($id, $value);
    }

    public function getQuestionId(): string
    {
        return $this->questionId;
    }

    public function getValue(): array
    {
        return $this->value;
    }
}