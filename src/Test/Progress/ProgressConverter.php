<?php

declare(strict_types=1);

namespace App\Test\Progress;

/**
 * Converts frontend progress to backend format.
 * Frontend format is [["1", "a"], ["2", "c"], ["3", ["b"]]]
 * Backend format is [1 => ["a"], 2 => ["c"], 3 => ["b"]]
 * @see ProgressConverterTest
 */
final class ProgressConverter
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