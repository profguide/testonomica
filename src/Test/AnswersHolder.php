<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Test;


use App\Entity\Answer;

class AnswersHolder
{
    /**@var Answer[] */
    private array $answers;

    /**@var Answer[] */
    private array $answersByQuestionId = [];

    public function __construct(array $answers)
    {
        $this->answers = $answers;
    }

    /**
     * @return Answer[]
     */
    public function getAll(): array
    {
        return $this->answers;
    }

    /**
     * Returns values for the Answer
     * @param string $questionId
     * @return array. May be empty, if no Answer was found or if no value
     */
    public function getValues(string $questionId): array
    {
        /**@var Answer $answer */
        $answer = $this->answersByQuestionId()[$questionId] ?? null;
        if ($answer) {
            return $answer->getValue();
        }
        return [];
    }

    /**
     * Calculates sum of values
     * @param $questionId
     * @return float|int
     */
    public function getValuesSum(string $questionId)
    {
        return array_sum($this->getValues($questionId));
    }

    private function answersByQuestionId(): array
    {
        if ($this->answersByQuestionId != null) {
            return $this->answersByQuestionId;
        }
        $answers = [];
        /**@var Answer $answer */
        foreach ($this->answers as $id => $answer) {
            $answers[$id] = $answer;
        }
        $this->answersByQuestionId = $answers;
        return $this->answersByQuestionId;
    }

    public function get(string $id): Answer
    {
        return $this->answers[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->answers[$id]);
    }

    public function remove(string $id): void
    {
        unset($this->answers[$id]);
    }
}