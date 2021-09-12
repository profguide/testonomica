<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\PaymentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;

class MigrateOldPaymentsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:migrate-old-payments';

    private EntityManagerInterface $em;

    public function configure()
    {
        $this->setName(self::$defaultName);
    }

    public function __construct(EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        echo 'came: 257C1E4F9D6942D666D512F701F609DA' . PHP_EOL;
        echo 'gen: '. md5('1:11383:uVtNe469XGI6yB6IKIKs') . PHP_EOL;
        return Command::SUCCESS;
    }

}