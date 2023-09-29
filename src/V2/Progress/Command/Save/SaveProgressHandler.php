<?php

declare(strict_types=1);

namespace App\V2\Progress\Command\Save;

use App\Entity\ProviderUserResult;
use App\Entity\Result;
use App\Test\Progress\ProgressSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @see SaveProgress
 * todo test
 */
#[AsMessageHandler]
final readonly class SaveProgressHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private ProgressSerializer $progressSerializer)
    {
    }

    public function __invoke(SaveProgress $command): Result
    {
        $test = $command->test;
        $user = $command->user;
        $progress = $command->progress;

        // todo Test Policy Validator
        //  проверить разрешён ли пользовталелю доступ к тесту,
        //  можно ли проходить два раза и тд - это всё регулируют политики тестов
        //  в данный момент не используется потому что ни с кем нет договорённости на этот счёт (пока нет).

        $result = Result::createAutoKey($test, $progress, $this->progressSerializer);
        $this->entityManager->persist($result);

        $providerUserResult = ProviderUserResult::create($user, $result, $test);
        $this->entityManager->persist($providerUserResult);

        $this->entityManager->flush();

        return $result;
    }
}