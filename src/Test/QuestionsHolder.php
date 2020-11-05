<?php
/**
 * @author: adavydov
 * @since: 04.11.2020
 */

namespace App\Test;


class QuestionsHolder
{
    private $questions;

    private $byGroups;

    public function __construct(array $questions)
    {
        $this->questions = $questions;
    }

    public function byGroups()
    {
        if ($this->byGroups != null) {
            return $this->byGroups;
        }
        $questions = [];
        /**@var Question $question */
        foreach ($this->questions as $question) {
            $questions[$question->getGroup()][] = $question;
        }
        $this->byGroups = $questions;
        return $this->byGroups;
    }

    public function group(string $name)
    {
        $groups = $this->byGroups();
        return $groups[$name];
    }
}