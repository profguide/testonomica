<?php
/**
 * @author: adavydov
 * @since: 04.11.2020
 */

namespace App\Test;

use App\Entity\Question;

class QuestionsHolder
{
    private array $questions;

    private array $byGroups = [];

    public function __construct(array $questions)
    {
        $this->questions = $questions;
    }

    public function byGroups(): array
    {
        if (!empty($this->byGroups)) {
            return $this->byGroups;
        }
        $groups = [];
        /**@var Question $question */
        foreach ($this->questions as $question) {
            $groups[$question->getVariety()][] = $question;
        }
        $this->byGroups = $groups;
        return $this->byGroups;
    }

    public function byGroupsMask(string $mask = "/(\w+)[-]\d+/"): array
    {
        $groups = [];
        /**@var Question $question */
        foreach ($this->questions as $question) {
            preg_match($mask, $question->getVariety(), $matches);
            $maskGroupName = $matches[1];
            $groups[$maskGroupName][] = $question;
        }
        return $groups;
    }

    public function group(string $name)
    {
        $groups = $this->byGroups();
        return $groups[$name];
    }

    public function get(string $id)
    {
        return $this->questions[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->questions[$id]);
    }

    public function remove(string $id): void
    {
        unset($this->questions[$id]);
    }

    /**
     * @return Question[]
     */
    public function getAll(): array
    {
        return $this->questions;
    }
}