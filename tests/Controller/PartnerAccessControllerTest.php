<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\ProviderAccessFixture;
use App\DataFixtures\ProviderFixture;
use App\DataFixtures\ProviderPaymentFixture;
use App\Repository\AccessRepository;
use App\Repository\ProviderPaymentRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class PartnerAccessControllerTest extends WebTestCase
{
    /**@var KernelBrowser */
    private $client;

    /**@var ProviderPaymentRepository */
    private $providerPaymentRepository;

    /**@var AccessRepository */
    private $accessRepository;

    /**@var ProviderRepository */
    private $providerRepository;

    protected function setUp()
    {
        $this->client = static::createClient();
        /**@var ProviderRepository $providerRepository */
        $this->providerRepository = self::$container->get(ProviderRepository::class);
        $this->providerPaymentRepository = self::$container->get(ProviderPaymentRepository::class);
        $this->accessRepository = self::$container->get(AccessRepository::class);
    }

    /**
     * Получение токена
     * Неоплаченный юзер
     * Ожидание: создан токен оплаты. При повторном обращении токен остается тем же
     */
    public function testGetTokenOnNotPayed()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $requestParams = [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::UNPAYED_USER,
        ];
        // делаем два запроса, чтобы получить два токена
        $token1 = $this->requestAccess($requestParams);
        $token2 = $this->requestAccess($requestParams);
        // токены должны быть одинаковыми
        $this->assertEquals($token1, $token2);
        // токен должен быть персистен и быть типа ProviderPayment
        $this->assertNotNull($this->providerPaymentRepository->findByToken($token1));
    }

    /**
     * Получение токена
     * Оплаченный юзер
     * Ожидание: создан токен доступа. При повторном обращении токены разные
     */
    public function testGetTokenOnPayed()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $requestParams = [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::PAYED_USER,
        ];
        // делаем два запроса, чтобы получить два токена
        $token1 = $this->requestAccess($requestParams);
        $token2 = $this->requestAccess($requestParams);
        // токены должны быть разными
        $this->assertFalse($token1 == $token2);
        // токены должены быть персистентны и быть типа ProviderAccess
        $this->assertNotNull($this->accessRepository->findOneByToken($token1));
        $this->assertNotNull($this->accessRepository->findOneByToken($token2));
    }

    /**
     * Получение токена
     * Бесплатный доступ
     * Ожидание: создан токен доступа. При повторном обращении токены разные
     */
    public function testGetTokenFree()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::PROFGUIDE);
        $requestParams = [
            'token' => $provider->getToken(),
            'user' => 'new-user',
        ];
        // делаем два запроса, чтобы получить два токена
        $token1 = $this->requestAccess($requestParams);
        $token2 = $this->requestAccess($requestParams);
        // токены должны быть разными
        $this->assertFalse($token1 == $token2);
        // токены должены быть персистентны и быть типа ProviderAccess
        $this->assertNotNull($this->accessRepository->findOneByToken($token1));
        $this->assertNotNull($this->accessRepository->findOneByToken($token2));
    }

    /**
     * Предоставление услуги по токену
     * Неоплаченный токен
     * Ожидание: редирект в робокассу, установлена кука оплаты
     */
    public function testProvideUnPayedToken()
    {
        // todo correct url
        $this->assertProvideIsRedirectingByToken(ProviderPaymentFixture::UNPAYED_TOKEN, 'GO_TO_PAYMENT_SERVICE');
        $this->assertCookie('payment', ProviderPaymentFixture::UNPAYED_TOKEN);
    }

    /**
     * Предоставление услуги по токену
     * Неиспользованный токен доступа
     * Ожидание: редирект в тест, установлена кука доступа
     */
    public function testProvideAccessToken()
    {
        $this->assertProvideIsRedirectingByToken(ProviderAccessFixture::TOKEN, '/tests/psychology/test_2/');
        $this->assertCookie('access', ProviderAccessFixture::TOKEN);
    }

    /***
     * Предоставление услуги по токену
     * Использованный токен доступа, но ранее установлена кука доступа
     * Ожидание: редирект в тест
     */
    public function testProvideAccessTokenUsedTokenButHasCookie()
    {
        $this->makeAccessUsed();
        $this->setAccessCookie(ProviderAccessFixture::TOKEN);
        $this->assertProvideIsRedirectingByToken(ProviderAccessFixture::TOKEN, '/tests/psychology/test_2/');
    }

    /**
     * Предоставление услуги по токену
     * Использованный токен доступа
     * Ожидание: 403
     */
    public function testProvideForbiddenUsedAccessTokenNoCookie()
    {
        $this->makeAccessUsed();
        $this->assertProvideIsDeniedByToken(ProviderAccessFixture::TOKEN);
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
        $this->assertProvideIsDeniedByToken(ProviderPaymentFixture::PAYED_TOKEN);
    }

    // Helpers bellow

    private function requestAccess(array $requestParams)
    {
        $this->client->request('POST', "/api/access/", $requestParams);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        return $this->parseToken($this->client->getResponse()->getContent());
    }

    private function requestProvide(array $requestParams)
    {
        $this->client->request('POST', "/api/access/provide/", $requestParams);
    }

    private function parseToken(string $string): string
    {
        return json_decode($string, true)['token'];
    }

    private function assertProvideIsDeniedByToken(string $token)
    {
        $this->requestProvide(['token' => $token]);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    private function assertProvideIsRedirectingByToken(string $token, string $url)
    {
        $this->requestProvide(['token' => $token]);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($url, $this->client->getResponse()->headers->get('location'));
    }

    private function assertCookie(string $name, string $token)
    {
        $cookies = $this->client->getResponse()->headers->getCookies();
        $this->assertEquals($name, $cookies[0]->getName());
        $this->assertEquals($token, $cookies[0]->getValue());
    }

    private function makeAccessUsed()
    {
        $access = $this->accessRepository->findOneByToken(ProviderAccessFixture::TOKEN);
        $access->setUsed();
        $this->accessRepository->save($access);
    }

    private function setAccessCookie(string $token)
    {
        $this->client->getCookieJar()->set(new Cookie('access', $token));
    }
}