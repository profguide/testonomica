<?php
/**
 * @author: adavydov
 * @since: 12.11.2020
 */

namespace App\Tests\Service;


use App\DataFixtures\ProviderFixture;
use App\Entity\Provider;
use App\Entity\Access;
use App\Entity\ProviderPayment;
use App\Entity\Service;
use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
use App\Service\ProviderPaymentService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProviderPaymentServiceTest extends KernelTestCase
{
    /**@var ProviderPaymentService */
    private $service;

    /**@var ServiceRepository */
    private $serviceRepository;

    /**@var Provider */
    private $provider;

    public function setUp()
    {
        self::bootKernel();
        $this->service = self::$container->get(ProviderPaymentService::class);
        $this->serviceRepository = self::$container->get(ServiceRepository::class);
        /**@var ProviderRepository $providerRepo */
        $providerRepo = self::$container->get(ProviderRepository::class);
        $this->provider = $providerRepo->findBySlug(ProviderFixture::TESTOMETRIKA);
    }

    /**
     * Первое обращение для юзера.
     * Expect: должен быть создан ProviderPayment/Payment со статусом Pending
     */
    public function testFirstTime()
    {
        $service = $this->loadService();
        $tokenable = $this->service->getToken($service, $this->provider, 'some_user');
        $this->assertEquals(ProviderPayment::class, get_class($tokenable));
    }

    /**
     * Повторное обращение для юзера, оплаты еще не было
     * Expect: объект платежа должен быть тот же самый
     */
    public function testRepeatedNotPayed()
    {
        $service = $this->loadService();
        $tokenable1 = $this->service->getToken($service, $this->provider, 'some_user');
        $tokenable2 = $this->service->getToken($service, $this->provider, 'some_user');
        $this->assertEquals($tokenable1, $tokenable2);
    }

    /**
     * Для юзера, который оплатил, должен быть сгенерирован токен доступа к тесту.
     * Каждое обращение создает новый токен.
     */
    public function testPayed()
    {
        $service = $this->loadService();
        $tokenable1 = $this->service->getToken($service, $this->provider, 'payed_user');
        $this->assertEquals(Access::class, get_class($tokenable1));
        $tokenable2 = $this->service->getToken($service, $this->provider, 'payed_user');
        $this->assertEquals(Access::class, get_class($tokenable2));
        $this->assertFalse($tokenable1 === $tokenable2);
    }

    private function loadService(): Service
    {
        return $this->serviceRepository->findOneBySlug('service_1');
    }
}