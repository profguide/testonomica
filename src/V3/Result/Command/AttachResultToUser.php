<?php

declare(strict_types=1);

namespace App\V3\Result\Command;

use App\Entity\ProviderUser;
use App\Entity\Result;

/**
 * @see AttachResultToUserHandler
 */
final readonly class AttachResultToUser
{
    public function __construct(public Result $result, public ProviderUser $user)
    {
    }
}