<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\DataFixtures;


use App\Entity\Provider;
use App\V2\Provider\Policy\Payment\PaymentPolicy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProviderFixture extends Fixture
{
    const PROFGUIDE = 'profguide';
    const TESTOMETRIKA = 'testometrika';

    public function load(ObjectManager $manager)
    {
        $profguide = new Provider();
        $profguide->setName('ПрофГид');
        $profguide->setSlug('profguide');
        $profguide->setToken('RHr1vrfDbYBCXr1NtQOMYRDpapqRtSzx');
        $profguide->setPaymentPolicy(PaymentPolicy::POST);
        $profguide->setAccessCount(0);
        $manager->persist($profguide);

        $testometrika = new Provider();
        $testometrika->setName('Тестометрика');
        $testometrika->setSlug('testometrika');
        $testometrika->setToken('JFeBtqLY3Tgw3uGG6b29hpdvfLlglGBE');
        $profguide->setPaymentPolicy(PaymentPolicy::PRE);
        $manager->persist($testometrika);

        $manager->flush();

        $this->addReference(self::PROFGUIDE, $profguide);
        $this->addReference(self::TESTOMETRIKA, $testometrika);
    }
}