<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Tests\Controller;


use App\Entity\Payment;
use App\Entity\Service;
use App\Payment\Robokassa;
use App\Repository\ServiceRepository;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

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
        $paymentExecuted = $this->createExecutedPayment();
        $this->client->request('POST', '/robokassa/done/', [
            'inv_id' => $paymentExecuted->getId(),
            'OutSum' => $paymentExecuted->getSum(),
            'SignatureValue' => Robokassa::getCrc2($paymentExecuted->getId(), $paymentExecuted->getSum())
        ]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("OK{$paymentExecuted->getId()}", $this->client->getResponse()->getContent());
    }

    /**
     *
     */
    public function testSuccess()
    {
        $paymentExecuted = $this->createExecutedPayment();
        $this->client->getCookieJar()->set(new Cookie('payment', $paymentExecuted->getId()));
        $this->client->request('POST', '/robokassa/success/');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/tests/psychology/test_2/', $this->client->getResponse()->headers->get('location'));
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
        return $this->serviceRepository->getOneBySlug('service_1');
    }
}