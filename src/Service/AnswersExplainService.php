<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Result;
use App\Repository\SourceRepositoryInterface;
use App\Test\Progress\ProgressSerializer;

/**
 * Create a map of questions and answers.
 * It is used by the quality control service.
 */
final readonly class AnswersExplainService
{
    public function __construct(
        private SourceRepositoryInterface $sourceRepository,
        private ProgressSerializer        $progressSerializer)
    {
    }

    public function rows(Result $result): array
    {
        $questionHolder = $this->sourceRepository->getAllQuestions($result->getTest());
        $answersHolder = $this->progressSerializer->deserialize($result->getData());
        $answersById = [];
        foreach ($answersHolder->answers as $answer) {
            $answersById[$answer->questionId] = $answer;
        }

        $rows = [];

        foreach ($questionHolder as $question) {
            $answer = $answersById[$question->getId()];
            $qItems = [];
            foreach ($question->getItems() as $item) {
                $qItems[] = [
                    'text' => $item->getText(),
                    'value' => $item->getValue(),
                    'opted' => $answer->hasValue($item->getValue())
                ];
            }

            $rows[] = [
                'id' => $question->getId(),
                'name' => $question->getName(),
                'text' => $question->getText(),
                'items' => $qItems
            ];
        }

        return $rows;
    }
}