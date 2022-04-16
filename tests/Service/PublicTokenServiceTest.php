<?php
/**
 * @author: adavydov
 * @since: 12.11.2020
 */

namespace App\Tests\Service;


use App\DataFixtures\ProviderFixture;
use App\DataFixtures\ProviderPaymentFixture;
use App\DataFixtures\ServiceFixture;
use App\Entity\Access;
use App\Entity\PaymentType;
use App\Entity\Provider;
use App\Entity\ProviderPayment;
use App\Entity\Service;
use App\Repository\ProviderPaymentRepository;
use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
use App\Service\ProviderUserPaymentService;
use App\Service\PublicTokenService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PublicTokenServiceTest extends KernelTestCase
{
    /**@var PublicTokenService */
    private $service;

    /**@var ServiceRepository */
    private $serviceRepository;

    /**@var Provider */
    private $provider;

    public function setUp()
    {
        self::bootKernel();
        $this->service = self::$container->get(PublicTokenService::class);
        $this->serviceRepository = self::$container->get(ServiceRepository::class);
        /**@var ProviderRepository $providerRepo */
        $providerRepo = self::$container->get(ProviderRepository::class);
        $this->provider = $providerRepo->findBySlug(ProviderFixture::TESTOMETRIKA);
    }

    /**
     * Первое обращение для юзера.
     * Expect: должен быть создан ProviderPayment/Payment со статусом Pending
     */
    public function test_create_payment_token_for_new_user()
    {
        $service = $this->loadService();

        $tokenable = $this->service->token($service, $this->provider, 'some_user', new PaymentType(PaymentType::DEFAULT));
        $this->assertEquals(ProviderPayment::class, get_class($tokenable));
    }

    /**
     * Повторное обращение для юзера, оплаты еще не было
     * Expect: объект платежа должен быть тот же самый
     */
    public function test_payment_token_always_same()
    {
        $service = $this->loadService();

        $tokenable1 = $this->service->token($service, $this->provider, 'some_user', new PaymentType(PaymentType::DEFAULT));
        $tokenable2 = $this->service->token($service, $this->provider, 'some_user', new PaymentType(PaymentType::DEFAULT));
        $this->assertEquals($tokenable1, $tokenable2);
    }

    /**
     * 1. Для юзера, который оплатил, должен быть сгенерирован токен доступа к тесту
     * 2. Каждое обращение должно создавать новый токен
     */
    public function test_access_token_always_different()
    {
        $service = $this->loadService();

        $tokenable1 = $this->service->token($service, $this->provider, ProviderPaymentFixture::PAID_USER, new PaymentType(PaymentType::DEFAULT));
        $this->assertEquals(Access::class, get_class($tokenable1));

        $tokenable2 = $this->service->token($service, $this->provider, ProviderPaymentFixture::PAID_USER, new PaymentType(PaymentType::DEFAULT));
        $this->assertEquals(Access::class, get_class($tokenable2));

        $this->assertFalse($tokenable1 === $tokenable2);
    }

    private function loadService(): Service
    {
        return $this->serviceRepository->getOneBySlug(ServiceFixture::SERVICE_1);
    }
}