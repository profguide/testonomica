<?php

declare(strict_types=1);

namespace App\V3\Result\Service;

use App\Entity\Provider;
use App\Entity\ProviderUser;
use App\Entity\Result;
use App\V2\Provider\Command\RegisterUser\RegisterProviderUser;
use App\V3\Result\Command\AttachResultToUser;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * Ассоциирует результат с пользователем клиента.
 * Пользователь будет создан если он не существует.
 * Этот сервис просто инкапсулирует эту бизнес-логику,
 * чтобы не исполнять в контроллере.
 * todo test
 */
class ResultAttachmentService
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function attachResult(Provider $provider, Result $result, string $userId): void
    {
        $user = $this->getOrCreateProviderUser($provider, $userId);
        $this->attachUser($result, $user);
    }

    private function getOrCreateProviderUser(Provider $provider, string $userId): ProviderUser
    {
        $envelop = $this->bus->dispatch(new RegisterProviderUser($provider, $userId));
        $handledStamp = $envelop->last(HandledStamp::class);
        return $handledStamp->getResult();
    }

    private function attachUser(Result $result, ProviderUser $user): void
    {
        $this->bus->dispatch(new AttachResultToUser($result, $user));
    }
}