<?php

declare(strict_types=1);

namespace App\Tests\Test\Progress;

use App\Test\Progress\RawAnswersConverter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RawAnswersConverterTest extends KernelTestCase
{
    public function testConvert()
    {
        $input = [["1", "a"], ["2", "c"], ["3", ["b"]]];

        $converter = new RawAnswersConverter();
        $output = $converter->convert($input);

        self::assertEquals([1 => ["a"], 2 => ["c"], 3 => ["b"]], $output);
    }
}