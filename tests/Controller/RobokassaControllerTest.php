<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\ServiceFixture;
use App\Entity\Payment;
use App\Entity\Service;
use App\Payment\PaymentBackRoute;
use App\Payment\Robokassa;
use App\Repository\ServiceRepository;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RobokassaControllerTest extends WebTestCase
{
    /**@var KernelBrowser */
    private $client;

    /**@var PaymentService */
    private $paymentService;

    /**@var ServiceRepository */
    private $serviceRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->paymentService = self::getContainer()->get(PaymentService::class);
        $this->serviceRepository = self::getContainer()->get(ServiceRepository::class);
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

    public function test_success_redirect_to_test()
    {
        // executed payment
        // the route is default
        $payment = $this->createExecutedPayment();
        $this->client->request('POST', '/robokassa/success/', ['InvId' => $payment->getId()]);

        $response = $this->client->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/tests/view/test_3/', $response->headers->get('location'));
        $this->assertCookie($response, 'access');
    }

    public function test_success_redirect_to_widget()
    {
        // executed payment
        $payment = Payment::init($this->service1(), 99);
        $payment->addStatusExecuted();
        // set redirect route
        $payment->setBackRoute(new PaymentBackRoute(PaymentBackRoute::TEST_WIDGET));
        $this->paymentService->save($payment);

        $this->client->request('POST', '/robokassa/success/', ['InvId' => $payment->getId()]);

        $response = $this->client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $location = $response->headers->get('location');
        $this->assertStringStartsWith('/tests/w/3/?token=', $location, 'Redirect url has to include this substring.');
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
        $payment = Payment::init($this->service1(), 99);
        $payment->addStatusExecuted();
        return $this->paymentService->save($payment);
    }

    private function assertCookie(Response $response, string $name)
    {
        $cookies = $response->headers->getCookies();
        $this->assertEquals($name, $cookies[0]->getName());
        $this->assertNotNull($cookies[0]->getValue());
    }

    private function service1(): Service
    {
        return $this->serviceRepository->getOneBySlug(ServiceFixture::SERVICE_1);
    }
}