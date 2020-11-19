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
    const PAYED_USER = 'payed_user';
    const UNPAYED_USER = 'not_payed_user';

    const PAYED_TOKEN = '5dc83aee-46d1-4f79-b6b2-a3e2a118c81e';
    const UNPAYED_TOKEN = '6e40be13-0dc9-4f57-adad-222b22b73549';

    public function load(ObjectManager $manager)
    {
        /**@var Provider $provider */
        $provider = $this->getReference(ProviderFixture::TESTOMETRIKA);

        $this->createPayed($manager, $provider);
        $this->createUnpayed($manager, $provider);

        $manager->flush();
    }

    private function createPayed(ObjectManager $manager, Provider $provider)
    {
        /**@var Payment $paymentPayed */
        $paymentPayed = $this->getReference(PaymentFixture::PAYED);
        $providerPaymentPayed = new ProviderPayment();
        $providerPaymentPayed->setPayment($paymentPayed);
        $providerPaymentPayed->setProvider($provider);
        $providerPaymentPayed->setUser(self::PAYED_USER);
        $providerPaymentPayed->setToken(self::PAYED_TOKEN);
        $manager->persist($providerPaymentPayed);
    }

    private function createUnpayed(ObjectManager $manager, Provider $provider)
    {
        /**@var Payment $paymentNotPayed */
        $paymentNotPayed = $this->getReference(PaymentFixture::NOT_PAYED);
        $providerPaymentNotPayed = new ProviderPayment();
        $providerPaymentNotPayed->setPayment($paymentNotPayed);
        $providerPaymentNotPayed->setProvider($provider);
        $providerPaymentNotPayed->setUser(self::UNPAYED_USER);
        $providerPaymentNotPayed->setToken(self::UNPAYED_TOKEN);
        $manager->persist($providerPaymentNotPayed);
    }

    public function getDependencies()
    {
        return [
            PaymentFixture::class
        ];
    }
}