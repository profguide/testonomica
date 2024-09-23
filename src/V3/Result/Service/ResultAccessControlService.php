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
        // Игнорируем проверку для очень старых результатов
        if ($this->isLegacyResult($result)) {
            return;
        }

        $test = $result->getTest();
        if ($test->isFree()) {
            return;
        }

        // Выполняем проверку ассоциации для новых результатов
        if (!$this->isResultAssociatedWithUser($result)) {
            throw new ResultUserAssociationMissingException($result);
        }
    }

    public function isResultAssociatedWithUser(Result $result): bool
    {
        return $this->usersResultsRepository->hasRecordByResult($result);
    }

    private function isLegacyResult(Result $result): bool
    {
        // This is the date when we started providing access
        // only to those results that were associated with the user.
        // Very old results do not have it.
        $userAssociationRequirementStartDate = new \DateTime('2024-09-24');
        return $result->getCreatedAt() < $userAssociationRequirementStartDate;
    }
}