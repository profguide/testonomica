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
    const NOT_PAYED = 'not_payed';
    const PAYED = 'payed';

    public function load(ObjectManager $manager)
    {
        /**@var Service $service */
        $service = $this->getReference(ServiceFixture::SERVICE_1);

        $paymentNotPayed = Payment::init($service, 349);
        $paymentNotPayed->addStatus(new PaymentStatus(PaymentStatus::STATUS_PENDING));
        $manager->persist($paymentNotPayed);

        $paymentPayed = Payment::init($service, 349);
        $paymentPayed->addStatus(new PaymentStatus(PaymentStatus::STATUS_PENDING));
        $paymentPayed->addStatus(new PaymentStatus(PaymentStatus::STATUS_EXECUTED));
        $manager->persist($paymentPayed);

        $manager->flush();

        $this->addReference(self::NOT_PAYED, $paymentNotPayed);
        $this->addReference(self::PAYED, $paymentPayed);
    }

    public function getDependencies()
    {
        return [
            ServiceFixture::class
        ];
    }
}