<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:migrate-old-payments')]
class MigrateOldPaymentsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        echo 'came: F5A302C2DE11ABEC9B96FFEFA93440E1' . PHP_EOL;
        echo 'wrong: e75b557dea1e284209e54e9760e43382' . PHP_EOL;
        echo 'gen: ' . md5('1:11384:CR33GHiwwaD5MD22xpag') . PHP_EOL;
        return Command::SUCCESS;
    }

}