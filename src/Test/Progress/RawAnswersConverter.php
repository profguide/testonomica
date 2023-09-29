<?php

declare(strict_types=1);

namespace App\Test\Progress;

use App\V2\Progress\RawAnswersToProgressConverter;

/**
 * @deprecated use RawAnswersToProgressConverter instead
 * @see RawAnswersToProgressConverter
 *
 * Converts frontend progress to backend format.
 * Frontend uses Array instead of Object
 * because Javascript tends to sort Objects automatically,
 * which is unacceptable.
 *
 * Frontend format is [["1", "a"], ["2", "c"], ["3", ["b"]]]
 * Backend format is [1 => ["a"], 2 => ["c"], 3 => ["b"]]
 *
 * @see RawAnswersConverterTest
 */
final class RawAnswersConverter
{
    public function convert(array $input): array
    {
        $output = [];

        foreach ($input as $row) {
            $output[$row[0]] = is_array($row[1]) ? $row[1] : [$row[1]];
        }

        return $output;
    }
}