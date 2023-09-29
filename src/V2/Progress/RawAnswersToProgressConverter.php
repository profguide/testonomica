<?php

declare(strict_types=1);

namespace App\V2\Progress;

use App\Entity\Answer;
use App\Test\Progress\Progress;

/**
 * Converts a raw answers from the request to Progress.
 * If request format changes converter must adopt as well.
 * The general idea of this converter to be completely
 * responsible for recognizing the all formats
 *
 * @see RawAnswersToProgressConverterTest
 */
final class RawAnswersToProgressConverter
{
    public function convert(array $requestedArray): Progress
    {
        $formattedAnswers = $this->formatRawAnswers($requestedArray);

        $answers = [];
        foreach ($formattedAnswers as $qId => $values) {
            $answers[$qId] = self::createAnswer($qId, $values);
        }

        return new Progress($answers);
    }

    /**
     * Converts frontend progress to backend format.
     * Frontend uses Array instead of Object
     * because Javascript tends to sort Objects automatically,
     * which is unacceptable.
     *
     * Frontend formats:
     *  - New format based on js Array: [["1", "a"], ["2", "c"], ["3", ["b"]]]
     *  - Old format based on js Object: ["1" => "a", "2" => ["b", "c"]]
     * Backend format is [1 => ["a"], 2 => ["c"], 3 => ["b"]]
     *
     * @param array $requestedAnswers
     * @return array
     */
    private function formatRawAnswers(array $requestedAnswers): array
    {
        // new format: sequential array
        if (array_keys($requestedAnswers)[0] === 0) {
            $output = [];

            foreach ($requestedAnswers as $row) {
                $output[$row[0]] = $row[1];
            }

            return $output;
        }

        // new format: associative array
        return $requestedAnswers;
    }

    private static function createAnswer(int $qId, $values): Answer
    {
        return new Answer((string)$qId, is_array($values) ? $values : [$values]);
    }
}