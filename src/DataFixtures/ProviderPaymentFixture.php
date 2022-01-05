<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\DataFixtures;

use App\Entity\Payment;
use App\Entity\Provider;
use App\Entity\ProviderPayment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProviderPaymentFixture extends Fixture implements DependentFixtureInterface
{
    const PAID_USER = 'paid';
    const PENDING_USER = 'pending';

    const PAID_TOKEN = '5dc83aee-46d1-4f79-b6b2-a3e2a118c81e';
    const PENDING_TOKEN = '6e40be13-0dc9-4f57-adad-222b22b73549';

    public function load(ObjectManager $manager)
    {
        /**@var Provider $provider */
        $provider = $this->getReference(ProviderFixture::TESTOMETRIKA);

        $this->createPaid($manager, $provider);
        $this->createUnpaid($manager, $provider);

        $manager->flush();
    }

    private function createPaid(ObjectManager $manager, Provider $provider)
    {
        /**@var Payment $executedPayment */
        $executedPayment = $this->getReference(PaymentFixture::PAID);

        $paid = new ProviderPayment();
        $paid->setPayment($executedPayment);
        $paid->setProvider($provider);
        $paid->setUser(self::PAID_USER);
        $paid->setToken(self::PAID_TOKEN);
        $manager->persist($paid);
    }

    private function createUnpaid(ObjectManager $manager, Provider $provider)
    {
        /**@var Payment $pendingPayment */
        $pendingPayment = $this->getReference(PaymentFixture::PENDING);

        $pending = new ProviderPayment();
        $pending->setPayment($pendingPayment);
        $pending->setProvider($provider);
        $pending->setUser(self::PENDING_USER);
        $pending->setToken(self::PENDING_TOKEN);
        $manager->persist($pending);
    }

    public function getDependencies()
    {
        return [
            PaymentFixture::class
        ];
    }
}