<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:fill-result-uuid')]
class FillResultUuidCommand extends Command
{
    public function __construct(private Connection $connection, string $name = null)
    {
        parent::__construct($name);
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $results = $this->connection->executeQuery('select * from result')->fetchAllAssociative();

        foreach ($results as $result) {
            if (!Uuid::isValid($result['new_id'])) {
                $uuid = Uuid::v4()->toBinary();

                $this->connection->executeStatement('UPDATE result SET new_id=:uuid WHERE id=:id', [
                    'uuid' => $uuid,
                    'id' => $result['id']
                ]);
            }
        }

        return Command::SUCCESS;
    }

}