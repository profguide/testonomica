<?php

declare(strict_types=1);

namespace App\Tests\V2\Progress;

use App\Entity\Answer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RawAnswersToProgressConverterTest extends KernelTestCase
{
    /**
     * Проверяет, что создаётся объект Progress из сырых ответов пользователя в старом формате (ассоциативный массив)
     *
     * Ожидаемый результат:
     * - создан объект Progress
     * - Progress содержит ответы в корректном формате
     */
    public function testConvertOldFormat()
    {
        $data = [
            "1" => "a",
            "2" => ["b", "c"]
        ];

        $converter = new \App\V2\Progress\RawAnswersToProgressConverter();
        $progress = $converter->convert($data);

        self::assertEquals($progress->answers['1'], new Answer('1', ['a']));
        self::assertEquals($progress->answers['2'], new Answer('2', ['b', 'c']));
    }

    /**
     * Проверяет, что создаётся объект Progress из сырых ответов пользователя в новом формате (последовательность)
     *
     * Ожидаемый результат:
     * - создан объект Progress
     * - Progress содержит ответы в корректном формате
     */
    public function testConvertNewFormat()
    {
        $data = [
            ["1", "a"], ["2", ['b', "c"]]
        ];

        $converter = new \App\V2\Progress\RawAnswersToProgressConverter();
        $progress = $converter->convert($data);

        self::assertEquals($progress->answers['1'], new Answer('1', ['a']));
        self::assertEquals($progress->answers['2'], new Answer('2', ['b', 'c']));
    }
}