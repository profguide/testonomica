<?php

declare(strict_types=1);

namespace App\V3\Result\Exception;

use App\Entity\Result;
use App\Exception\AppException;

final class ResultUserAssociationMissingException extends AppException
{
    public function __construct(public readonly Result $result, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        if (empty($message)) {
            $this->message = "Result has no association with the user.";
        }
        parent::__construct($message, $code, $previous);
    }
}