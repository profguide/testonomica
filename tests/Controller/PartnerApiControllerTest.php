<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Tests\Controller;


use App\DataFixtures\ProviderFixture;
use App\DataFixtures\ProviderPaymentFixture;
use App\Repository\AccessRepository;
use App\Repository\ProviderPaymentRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PartnerApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;

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
     * Ожидание:
     * - создан токен оплаты
     * - при повторном обращении токен остается тем же
     */
    public function testGetTokenOnNotPayed()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $requestParams = [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::UNPAYED_USER,
        ];
        // делаем два запроса, чтобы получить два токена
        $token1 = $this->requestToken($requestParams);
        $token2 = $this->requestToken($requestParams);
        // токены должны быть одинаковыми
        $this->assertEquals($token1, $token2);
        // токен должен быть персистен и быть типа ProviderPayment
        $this->assertNotNull($this->providerPaymentRepository->findByToken($token1));
    }

    /**
     * Получение токена
     * Оплаченный юзер
     * Ожидание:
     * - создан токен доступа
     * - при повторном обращении токены разные
     */
    public function testGetTokenOnPayed()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $requestParams = [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::PAYED_USER,
        ];
        // делаем два запроса, чтобы получить два токена
        $token1 = $this->requestToken($requestParams);
        $token2 = $this->requestToken($requestParams);
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
        $token1 = $this->requestToken($requestParams);
        $token2 = $this->requestToken($requestParams);
        // токены должны быть разными
        $this->assertFalse($token1 == $token2);
        // токены должены быть персистентны и быть типа ProviderAccess
        $this->assertNotNull($this->accessRepository->findOneByToken($token1));
        $this->assertNotNull($this->accessRepository->findOneByToken($token2));
    }

    // Helpers bellow

    private function requestToken(array $requestParams): string
    {
        $this->client->request('POST', "/partner/api/token/", $requestParams);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        return $this->parseToken($this->client->getResponse()->getContent());
    }

    private function parseToken(string $string): string
    {
        return json_decode($string, true)['token'];
    }
}