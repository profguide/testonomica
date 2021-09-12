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
        echo 'came: F5A302C2DE11ABEC9B96FFEFA93440E1' . PHP_EOL;
        echo 'wrong: e75b557dea1e284209e54e9760e43382' . PHP_EOL;
        echo 'gen: '. md5('1:11384:CR33GHiwwaD5MD22xpag') . PHP_EOL;
        return Command::SUCCESS;
    }

}