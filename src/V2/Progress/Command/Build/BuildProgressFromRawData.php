<?php

declare(strict_types=1);

namespace App\V2\Progress\Command\Build;

use App\Entity\Test;

/**
 * @see BuildProgressFromRawDataHandler
 */
final readonly class BuildProgressFromRawData
{
    public function __construct(public Test $test, public array $data)
    {
    }
}