<?php

declare(strict_types=1);

namespace App\V3\Result\Service;

use App\Entity\Result;
use App\Entity\Test;
use App\Test\Progress\Progress;
use App\V3\Progress\Command\Save\SaveProgress;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * Создан для упрощения клиентского кода.
 * todo test
 */
class ProgressService
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function saveProgress(Test $test, Progress $progress): Result
    {
        $envelop = $this->bus->dispatch(new SaveProgress($test, $progress));
        $handledStamp = $envelop->last(HandledStamp::class);
        if (!$handledStamp) {
            throw new \RuntimeException('SaveProgress handler did not return a result.');
        }

        return $handledStamp->getResult();
    }
}