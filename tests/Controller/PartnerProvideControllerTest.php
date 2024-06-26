<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\AccessFixture;
use App\DataFixtures\ProviderPaymentFixture;
use App\Entity\Payment;
use App\Repository\AccessRepository;
use App\Repository\ProviderPaymentRepository;
use App\Repository\ProviderRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class PartnerProvideControllerTest extends WebTestCase
{
    /**@var KernelBrowser */
    private $client;

    /**@var AccessRepository */
    private $accessRepository;

    /**@var ProviderPaymentRepository */
    private $providerPaymentRepository;

    /**@var ProviderRepository */
    private $providerRepository;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        /**@var ProviderRepository $providerRepository */
        $this->providerRepository = self::getContainer()->get(ProviderRepository::class);
        $this->providerPaymentRepository = self::getContainer()->get(ProviderPaymentRepository::class);
        $this->accessRepository = self::getContainer()->get(AccessRepository::class);
    }

    /**
     * Предоставление услуги по токену
     * Неоплаченный токен
     * Ожидание: редирект в робокассу, установлена кука оплаты
     */
    public function testProvideUnPayedToken()
    {
        $this->assertProvideIsRedirectingByToken(ProviderPaymentFixture::PENDING_TOKEN);
        $this->assertStringStartsWith(
            'https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=testonomica',
            $this->client->getResponse()->headers->get('location'));
    }

    /**
     * Предоставление услуги по токену
     * Неиспользованный токен доступа
     * Ожидание: редирект в тест, установлена кука доступа
     */
    public function testProvideAccessToken()
    {
        $this->assertProvideIsRedirectingByToken(AccessFixture::TOKEN);
        $this->assertCookie('access', $this->accessRepository->findOneByToken(AccessFixture::TOKEN)->getToken());
        $this->assertEquals('/tests/view/proforientation-v2/', $this->client->getResponse()->headers->get('location'));
    }

    /***
     * Предоставление услуги по токену
     * Использованный токен доступа, но ранее установлена кука доступа
     * Ожидание: редирект в тест
     */
    public function testProvideAccessTokenUsedTokenButHasCookie()
    {
        $this->makeAccessUsed();
        $this->setAccessCookie(AccessFixture::TOKEN);
        $this->assertProvideIsRedirectingByToken(AccessFixture::TOKEN);
        $this->assertEquals('/tests/view/proforientation-v2/', $this->client->getResponse()->headers->get('location'));
    }

    /**
     * Предоставление услуги по токену
     * Использованный токен доступа
     * Ожидание: 403
     */
    public function testProvideForbiddenUsedAccessTokenNoCookie()
    {
        $this->makeAccessUsed();
        $this->assertProvideIsDeniedByToken(AccessFixture::TOKEN);
    }

    /**
     * Предоставление услуги по токену
     * Неизвестный токен
     * Ожидание: 403
     */
    public function testProvideForbiddenUnknownToken()
    {
        $this->assertProvideIsDeniedByToken('non-existed-token');
    }

    /**
     * Предоставление услуги по токену
     * Токен оплаты уже был оплачен
     * Ожидание: 403
     */
    public function testProvideForbiddenPayedToken()
    {
        $this->assertProvideIsDeniedByToken(ProviderPaymentFixture::PAID_TOKEN);
    }


    private function assertProvideIsDeniedByToken(string $token): void
    {
        $this->requestProvide(['token' => $token]);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    private function assertProvideIsRedirectingByToken(string $token): void
    {
        $this->requestProvide(['token' => $token]);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    private function assertCookie(string $name, string $token): void
    {
        $cookies = $this->client->getResponse()->headers->getCookies();
        $this->assertEquals($name, $cookies[0]->getName());
        $this->assertEquals($token, $cookies[0]->getValue());
    }

    private function makeAccessUsed(): void
    {
        $access = $this->accessRepository->findOneByToken(AccessFixture::TOKEN);
        $access->setUsed();
        $this->accessRepository->save($access);
    }

    private function setAccessCookie(string $token): void
    {
        $this->client->getCookieJar()->set(new Cookie('access', $token));
    }

    private function requestProvide(array $requestParams): void
    {
        $this->client->request('POST', "/partner/provide/", $requestParams);
    }

    private function paymentByToken(string $token): Payment
    {
        return $this->providerPaymentRepository
            ->findByToken($token)
            ->getPayment();
    }
}