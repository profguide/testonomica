<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\DataFixtures;


use App\Entity\Payment;
use App\Entity\PaymentStatus;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentFixture extends Fixture implements DependentFixtureInterface
{
    const PENDING = 'pending';
    const PAID = 'paid';

    public function load(ObjectManager $manager)
    {
        /**@var Service $service */
        $service = $this->getReference(ServiceFixture::SERVICE_1);

        $pending = Payment::init($service, 349);
        $pending->addStatus(new PaymentStatus(PaymentStatus::STATUS_PENDING));
        $manager->persist($pending);

        $paid = Payment::init($service, 349);
        $paid->addStatus(new PaymentStatus(PaymentStatus::STATUS_EXECUTED));
        $manager->persist($paid);

        $manager->flush();

        $this->addReference(self::PENDING, $pending);
        $this->addReference(self::PAID, $paid);
    }

    public function getDependencies()
    {
        return [
            ServiceFixture::class
        ];
    }
}