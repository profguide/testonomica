<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\ServiceFixture;
use App\Entity\Payment;
use App\Entity\Service;
use App\Payment\Robokassa;
use App\Repository\ServiceRepository;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RobokassaControllerTest extends WebTestCase
{
    /**@var KernelBrowser */
    private $client;

    /**@var PaymentService */
    private $paymentService;

    /**@var ServiceRepository */
    private $serviceRepository;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->paymentService = self::$container->get(PaymentService::class);
        $this->serviceRepository = self::$container->get(ServiceRepository::class);
    }

    /**
     *
     */
    public function testDone()
    {
        $executedPayment = $this->createExecutedPayment();
        $this->client->request('POST', '/robokassa/done/', [
            'inv_id' => $executedPayment->getId(),
            'OutSum' => $executedPayment->getSum(),
            'SignatureValue' => Robokassa::crc2(
                $executedPayment->getId(),
                $executedPayment->getSum(),
                $executedPayment->isTestMode())
        ]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("OK{$executedPayment->getId()}", $this->client->getResponse()->getContent());
    }

    /**
     *
     */
    public function testSuccess()
    {
        $executedPayment = $this->createExecutedPayment();
        $this->client->request('POST', '/robokassa/success/', ['InvId' => $executedPayment->getId()]);
        // $this->client->getCookieJar()->set(new Cookie('payment', $executedPayment->getId()));
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/tests/business/proforientation-v2/', $this->client->getResponse()->headers->get('location'));
        $this->assertCookie('access');
    }

    /**
     *
     */
    public function testFail()
    {
        $this->client->request('POST', '/robokassa/fail/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Оплата не прошла. Вернитесь на сайт партнёра.',
            $this->client->getResponse()->getContent());
    }

    private function createExecutedPayment(): Payment
    {
        $payment = Payment::init($this->loadService(), 99);
        $payment->addStatusExecuted();
        return $this->paymentService->save($payment);
    }

    private function assertCookie(string $name)
    {
        $cookies = $this->client->getResponse()->headers->getCookies();
        $this->assertEquals($name, $cookies[0]->getName());
        $this->assertNotNull($cookies[0]->getValue());
    }

    private function loadService(): Service
    {
        return $this->serviceRepository->getOneBySlug(ServiceFixture::SERVICE_1);
    }
}