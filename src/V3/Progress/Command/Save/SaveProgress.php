<?php

declare(strict_types=1);

namespace App\V3\Progress\Command\Save;

use App\Entity\Test;
use App\Test\Progress\Progress;

/**
 * @see SaveProgressHandler
 */
final readonly class SaveProgress
{
    public function __construct(public Test $test, public Progress $progress)
    {
    }
}