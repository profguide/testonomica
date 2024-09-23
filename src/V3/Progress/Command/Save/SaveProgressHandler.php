<?php

declare(strict_types=1);

namespace App\V3\Progress\Command\Save;

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
        $progress = $command->progress;

        $result = Result::createAutoKey($test, $progress, $this->progressSerializer);
        $this->entityManager->persist($result);

        $this->entityManager->flush();

        return $result;
    }
}