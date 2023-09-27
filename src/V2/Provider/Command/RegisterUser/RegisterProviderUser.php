<?php

declare(strict_types=1);

namespace App\V2\Provider\Command\RegisterUser;

use App\Entity\Provider;

/**
 * @see RegisterProviderUserHandler
 */
final readonly class RegisterProviderUser
{
    public function __construct(public Provider $provider, public string $userId)
    {
    }
}