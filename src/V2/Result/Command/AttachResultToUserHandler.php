<?php

declare(strict_types=1);

namespace App\V2\Result\Command;

use App\Entity\Provider;
use App\Entity\ProviderUser;
use App\Entity\ProviderUserResult;
use App\Entity\Result;
use App\Entity\Test;
use App\Repository\ProviderUsersResultsRepository;
use App\V2\Result\Exception\ResultAlreadyAttachedAnotherUserException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @see AttachResultToUser
 * @see AttachResultToUserTest
 *
 * Создаёт связь результата с пользователем провайдера.
 * Перед этим валидирует условия в соответствии с политикой тестов.
 */
#[AsMessageHandler]
class AttachResultToUserHandler
{
    public function __construct(
        private EntityManagerInterface         $entityManager,
        private ProviderUsersResultsRepository $usersResultsRepository)
    {
    }

    public function __invoke(AttachResultToUser $command): void
    {
        $result = $command->result;
        $test = $result->getTest();
        $user = $command->user;
        $provider = $user->getProvider();

        if ($this->isAttached($result, $user)) {
            return;
        }

        $this->guardAnotherUserAttached($result, $user);
        $this->guardLicensePolicy($provider, $user, $test);

        $this->attach($result, $user, $test);
    }

    private function attach(Result $result, ProviderUser $user, Test $test): void
    {
        $providerUserResult = ProviderUserResult::create($user, $result, $test);

        $this->entityManager->persist($providerUserResult);
        $this->entityManager->flush();
    }

    private function isAttached(Result $result, ProviderUser $user): bool
    {
        return $this->usersResultsRepository->hasByResultAndUser($result, $user);
    }

    private function guardAnotherUserAttached(Result $result, ProviderUser $user): void
    {
        if ($this->usersResultsRepository->hasAnotherUserAttached($result, $user)) {
            throw new ResultAlreadyAttachedAnotherUserException('Result is already has the other user attached.');
        }
    }

    private function guardLicensePolicy(Provider $provider, ProviderUser $user, Test $test)
    {
        // todo Test Policy Validator
        //  проверить разрешён ли пользовталелю доступ к тесту,
        //  можно ли проходить два раза и тд - это всё регулируют политики тестов
        //  в данный момент не используется потому что ни с кем нет договорённости на этот счёт (пока нет).
    }
}