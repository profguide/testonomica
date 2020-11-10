<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Tests\Controller;


use App\Entity\Provider;
use App\Repository\AccessRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProviderPaymentRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PartnerAccessControllerTest extends WebTestCase
{
    /**@var KernelBrowser */
    private $client;

    /**@var Provider */
    private $provider;

    /**@var ProviderPaymentRepository */
    private $providerPaymentRepository;

    /**@var AccessRepository */
    private $accessRepository;

    protected function setUp()
    {
        $this->client = static::createClient();
        /**@var ProviderRepository $providerRepository */
        $providerRepository = self::$container->get(ProviderRepository::class);
        $this->provider = $providerRepository->findBySlug('testometrika');
        $this->providerPaymentRepository = self::$container->get(ProviderPaymentRepository::class);
        $this->accessRepository = self::$container->get(AccessRepository::class);
    }

    public function testInitial()
    {
        $requestParams = [
            'token' => $this->provider->getToken(),
            'user' => 1,
        ];
        $this->client->request('POST', "/api/access/", $requestParams);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $token1 = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $this->assertNotNull($this->providerPaymentRepository->findByToken($token1));

        // повтор должен вернуть тот же токен
        $this->client->request('POST', "/api/access/", $requestParams);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $token2 = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $this->assertEquals($token1, $token2);
    }
}