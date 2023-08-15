<?php
/**
 * @author: adavydov
 * @since: 12.11.2020
 */

namespace App\Tests\Repository;


use App\DataFixtures\ProviderFixture;
use App\DataFixtures\ServiceFixture;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use App\Entity\PaymentType;
use App\Entity\Provider;
use App\Entity\ProviderPayment;
use App\Entity\Service;
use App\Repository\PaymentRepository;
use App\Repository\ProviderPaymentRepository;
use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentRepositoryTest extends KernelTestCase
{
    /**@var PaymentRepository */
    private $paymentRepository;

    /**@var ServiceRepository */
    private $serviceRepository;

    /**@var ProviderPaymentRepository */
    private $providerPaymentRepository;

    /**@var Provider */
    private $provider;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->paymentRepository = static::getContainer()->get(PaymentRepository::class);
        $this->providerPaymentRepository = static::getContainer()->get(ProviderPaymentRepository::class);
        $this->serviceRepository = static::getContainer()->get(ServiceRepository::class);
        $providerRepo = static::getContainer()->get(ProviderRepository::class);
        $this->provider = $providerRepo->findBySlug(ProviderFixture::TESTOMETRIKA);
    }

    /**
     * Убедимся, что Payment::isExecuted() работает правильно
     */
    public function testIsExecuted()
    {
        $payment = $this->createPayment();
        $providerPayment = $this->createProviderPayment($payment, '123');

        // Изначально не выполнено
        $this->assertFalse($providerPayment->getPayment()->isExecuted(), "Status must not be executed");

        // Добавим статус ожидания, должно быть не выполнено
        $payment->addStatus(new PaymentStatus(PaymentStatus::STATUS_PENDING));
        $this->paymentRepository->update($payment);
        $this->assertFalse($providerPayment->getPayment()->isExecuted(), "Status must be not executed");

        // Добавим статус выполнено, должно быть выполнено
        $payment->addStatus(new PaymentStatus(PaymentStatus::STATUS_EXECUTED));
        $this->paymentRepository->update($payment);
        $this->assertTrue($providerPayment->getPayment()->isExecuted(), "Status must be executed");
    }

    private function createPayment(): Payment
    {
        $payment = Payment::init($this->loadService(), 349);
        return $this->paymentRepository->save($payment);
    }

    private function createProviderPayment(Payment $payment, string $user)
    {
        $providerPayment = ProviderPayment::init($payment, $this->provider, $user, new PaymentType(PaymentType::DEFAULT));
        return $this->providerPaymentRepository->save($providerPayment);
    }

    private function loadService(): Service
    {
        return $this->serviceRepository->getOneBySlug(ServiceFixture::SERVICE_1);
    }
}