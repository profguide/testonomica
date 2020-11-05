<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Test;


use App\Entity\Answer;

class AnswersHolder
{
    private $answers;

    private $answersByQuestionId;

    public function __construct(array $answers)
    {
        $this->answers = $answers;
    }

    public function getAll(): array
    {
        return $this->answers;
    }

    /**
     * Returns value for the Answer
     * If Answer not found return null
     * @param string $questionId
     * @return string|null
     */
    public function getValue(string $questionId): ?string
    {
        /**@var Answer $answer */
        $answer = $this->answersByQuestionId()[$questionId] ?? null;
        if ($answer) {
            return $answer->getValue();
        }
        return null;
    }

    private function answersByQuestionId()
    {
        if ($this->answersByQuestionId != null) {
            return $this->answersByQuestionId;
        }
        $answers = [];
        /**@var Answer $answer */
        foreach ($this->answers as $answer) {
            $answers[$answer->getQuestionId()] = $answer;
        }
        $this->answersByQuestionId = $answers;
        return $this->answersByQuestionId;
    }
}