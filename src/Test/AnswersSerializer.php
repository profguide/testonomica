<?php
/**
 * @author: adavydov
 * @since: 01.11.2020
 */

namespace App\Test;


use App\Entity\Answer;

// todo удалить после переезда хранилища во фронтенд
class AnswersSerializer
{
    /**
     * @param Answer[] $answers
     * @return string
     */
    public function serialize(array $answers): string
    {
        $simpleAnswers = [];
        foreach ($answers as $answer) {
            $simpleAnswers[$answer->getQuestionId()] = $answer->getValue();
        }
        return json_encode($simpleAnswers);
        //$this->serializer->serialize($simpleAnswers, 'json');
    }

    public function deserialize(string $json): array
    {
        $jsonData = json_decode($json, true);
        $answers = [];
        foreach ($jsonData as $id => $values) {
            $answers[$id] = new Answer($id, $values);
        }
        return $answers;
        //return $this->serializer->deserialize($json, 'App\Entity\Answer[]', 'json', [AbstractNormalizer::ATTRIBUTES => ['value']]);
    }
}