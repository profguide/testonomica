<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Provider;
use App\Entity\ProviderUser;
use App\Repository\ProviderRepository;
use App\Repository\ProviderUserRepository;
use App\V2\Provider\Policy\Payment\PaymentPolicy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:play')]
final class PlayCommand extends Command
{
    public function __construct(
        private readonly ProviderUserRepository $repository,
        private readonly ProviderRepository $providers,
        private readonly EntityManagerInterface $em,
        string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $policy = PaymentPolicy::PRE;
        echo "Payment policy: $policy->name";


//        $provider = $this->providers->find(1);
//
//        $user = new ProviderUser();
//        $user->setExtUserId((string)mt_rand());
//        $user->setProvider($provider);
//
//        $this->em->persist($user);
//        $this->em->flush();
//
//        /**@var ProviderUser[] $users */
//        $users = $this->repository->findAll();
//        foreach ($users as $user) {
//            echo $user->getId() . PHP_EOL;
//        }
//
//        /**@var ProviderUser $find*/
//        $find = $this->repository->find('018ad1b9-ea48-758f-8aa8-c15b51df587b');
//        echo 'Found' . PHP_EOL;
//        echo $find->getId() . ' : ' . $find->getExtUserId() . PHP_EOL;

        return self::SUCCESS;
    }
}