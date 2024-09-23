<?php

declare(strict_types=1);

namespace App\V3\Result\Service;

use App\Entity\Result;
use App\Repository\ProviderUsersResultsRepository;
use App\Tests\V3\Result\Service\ResultAccessControlServiceTest;
use App\V3\Result\Exception\ResultUserAssociationMissingException;

/**
 * Checks whether the result is available for display.
 * @see ResultAccessControlServiceTest
 */
class ResultAccessControlService
{
    public function __construct(private readonly ProviderUsersResultsRepository $usersResultsRepository)
    {
    }

    public function guardResultAccess(Result $result): void
    {
        $test = $result->getTest();
        if ($test->isFree()) {
            return;
        }

        if (!$this->usersResultsRepository->hasRecordByResult($result)) {
            throw new ResultUserAssociationMissingException($result);
        }
    }
}