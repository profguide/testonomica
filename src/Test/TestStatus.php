<?php

namespace App\Test;

/**
 * @author: adavydov
 * @since: 25.10.2020
 */
class TestStatus
{
    const NONE = 'none';
    const FINISHED = 'finished';
    const PROGRESS = 'progress';

    private $status;

    private function __construct(string $status)
    {
        $this->status = $status;
    }

    public static function none()
    {
        return new TestStatus(self::NONE);
    }

    public static function finished()
    {
        return new TestStatus(self::FINISHED);
    }

    public static function progress()
    {
        return new TestStatus(self::PROGRESS);
    }

    public function __toString()
    {
        return $this->status;
    }
}