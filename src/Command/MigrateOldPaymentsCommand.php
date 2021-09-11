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
        // array('id' => '1','provider_user_id' => '1','provider_user_email' => 'asd@asd.ru','token' => '7aeb1d17f4f74b71bb85ec2b0f233696','created_at' => '2020-07-23 20:36:58.875789','payed_at' => '2020-07-23 20:36:58.875505','sum' => '500.00','provider_id' => '1','quiz_id' => '4','is_applied' => '0','is_test' => '0'),
        $data = include('../../api_providerpayment.php');
        foreach ($data as $row) {
            if (empty($row['payed_at'])) {
                continue;
            }
            $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s.u', $row['created_at'])->format('Y-m-d H:i:s');
            // payment - id
            $this->em->getConnection()->insert('payment', [
                'id' => $row['id'],
                'service_id' => 1,
                'sum' => (int)$row['sum'],
                'created_at' => $dateTime
            ]);
            $this->em->getConnection()->insert('provider_payment', [
                'payment_id' => $row['id'],
                'provider_id' => $row['provider_id'],
                'token' => Uuid::v4(),
                'user' => $row['provider_user_id'],
            ]);
            $this->em->getConnection()->insert('payment_status', [
                'payment_id' => $row['id'],
                'status' => PaymentStatus::STATUS_EXECUTED,
                'created_at' => $dateTime,
            ]);
            echo "save\n";
        }
//        $query = $this->em->createNativeQuery('insert into payment (id, service_id, sum, created_at)
//            values (1, 1, 500, 2020-12-12)');
//        $query->execute();


        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }

}