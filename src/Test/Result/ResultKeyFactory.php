<?php

declare(strict_types=1);

namespace App\Test\Result;

use Symfony\Component\Uid\Uuid;

final class ResultKeyFactory
{
    public function create(mixed $key): ResultKey
    {
        if (Uuid::isValid($key)) {
            return new ResultUuidKey(new Uuid($key));
        } elseif (is_string($key)) {
            return new ResultHashKey($key);
        }

        throw new \InvalidArgumentException("Unsupported result key type.");
    }
}