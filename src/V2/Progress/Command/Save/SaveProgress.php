<?php

declare(strict_types=1);

namespace App\V2\Progress\Command\Save;

use App\Entity\ProviderUser;
use App\Entity\Test;
use App\Test\Progress\Progress;

/**
 * @see SaveProgressHandler
 */
final readonly class SaveProgress
{
    public function __construct(public Test $test, public ProviderUser $user, public Progress $progress)
    {
    }
}