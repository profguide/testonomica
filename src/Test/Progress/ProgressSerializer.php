<?php

declare(strict_types=1);

namespace App\Test\Progress;

use App\Entity\Answer;

/**
 * Это новый и правильный сериализатор.
 * Старый AnswersSerializer нужно удалить и везде всё заменить
 */
class ProgressSerializer
{
    /**
     * @param Progress $progress
     * @return string
     */
    public function serialize(Progress $progress): string
    {
        $simpleAnswers = [];
        foreach ($progress->answers as $answer) {
            $simpleAnswers[$answer->getQuestionId()] = $answer->getValue();
        }
        return json_encode($simpleAnswers);
    }

    public function deserialize(string $json): Progress
    {
        $jsonData = json_decode($json, true);
        $answers = [];
        foreach ($jsonData as $id => $values) {
            $answers[$id] = new Answer($id, $values);
        }
        return new Progress($answers);
    }
}