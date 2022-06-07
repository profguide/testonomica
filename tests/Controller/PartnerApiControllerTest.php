<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Tests\Controller;


use App\Controller\PartnerApiController;
use App\DataFixtures\ProviderFixture;
use App\DataFixtures\ProviderPaymentFixture;
use App\DataFixtures\ServiceFixture;
use App\Entity\PaymentType;
use App\Entity\Provider;
use App\Repository\AccessRepository;
use App\Repository\ProviderPaymentRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @see PartnerApiController
 */
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
        $this->providerRepository = self::getContainer()->get(ProviderRepository::class);
        $this->providerPaymentRepository = self::getContainer()->get(ProviderPaymentRepository::class);
        $this->accessRepository = self::getContainer()->get(AccessRepository::class);
    }

    public function testErrorEmptyProvider()
    {
        $this->client->request('POST', "/partner/api/token/", [
            'token' => null,
            'user' => ProviderPaymentFixture::PENDING_USER,
            'service' => ServiceFixture::SERVICE_1,
        ]);
        self::assertEquals(412, $this->client->getResponse()->getStatusCode());
        $this->responseWithErrorText('Token must be specified.');
    }

    public function testErrorWrongProvider()
    {
        $this->client->request('POST', "/partner/api/token/", [
            'token' => 'wrong-provider-token',
            'user' => ProviderPaymentFixture::PENDING_USER,
            'service' => ServiceFixture::SERVICE_1,
        ]);
        self::assertEquals(412, $this->client->getResponse()->getStatusCode());
        $this->responseWithErrorText('Provider not found.');
    }

    public function testErrorEmptyService()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $this->client->request('POST', "/partner/api/token/", [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::PENDING_USER,
            'service' => null,
        ]);
        self::assertEquals(412, $this->client->getResponse()->getStatusCode());
        $this->responseWithErrorText('Service must be specified.');
    }

    public function testErrorWrongService()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $this->client->request('POST', "/partner/api/token/", [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::PENDING_USER,
            'service' => 'non-existent-service-name',
        ]);
        self::assertEquals(412, $this->client->getResponse()->getStatusCode());
        $this->responseWithErrorText('Service "non-existent-service-name" not found.');
    }

    public function testErrorEmptyUser()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $this->client->request('POST', "/partner/api/token/", [
            'token' => $provider->getToken(),
            'user' => null,
            'service' => ServiceFixture::SERVICE_1,
        ]);
        self::assertEquals(412, $this->client->getResponse()->getStatusCode());
        $this->responseWithErrorText('User must be specified.');
    }

    public function testErrorWrongPaymentType()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $this->client->request('POST', "/partner/api/token/", [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::PENDING_USER,
            'service' => ServiceFixture::SERVICE_1,
            'payment_type' => 'wrong-payment-type'
        ]);
        self::assertEquals(412, $this->client->getResponse()->getStatusCode());
        $this->responseWithErrorText('Unsupported payment type: wrong-payment-type.');
    }

    /**
     * Получение платёжного токена
     * Условие:
     * - для указанного юзера нет оплаченных заказов
     * Ожидание:
     * - создан токен оплаты
     * - при повторном обращении токен остается тем же
     */
    public function testGetPaidTokenForUnpaidUser()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $requestParams = [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::PENDING_USER,
            'service' => ServiceFixture::SERVICE_1,
        ];
        $token = $this->requestToken($requestParams);
        $this->assertTokenIsPayment($token);

        $tokenRepeat = $this->requestToken($requestParams);
        $this->assertEquals($token, $tokenRepeat, 'Tokens should be equal every time.');
    }

    /**
     * Получение пропускного токена
     * Условие
     * - для указанного юзера есть оплаченный заказ
     * Ожидание:
     * - создан токен доступа
     * - при повторном обращении токены разные
     */
    public function testGetTokenOnPayed()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $requestParams = [
            'token' => $provider->getToken(),
            'user' => ProviderPaymentFixture::PAID_USER,
            'service' => ServiceFixture::SERVICE_1,
        ];

        $token = $this->requestToken($requestParams);
        $this->assertTokenIsAccess($token);

        $tokenRepeat = $this->requestToken($requestParams);
        $this->assertTokenIsAccess($tokenRepeat);

        $this->assertNotEquals($token, $tokenRepeat, 'Tokens should be different every time.');
    }

    /**
     * Получение пропускного токена без реальной оплаты
     * Условие:
     * - указан параметр для создания пропусков
     * Ожидание:
     * - создан доверительный платёж
     * - платёж числится оплаченным
     * - создан токен доступа
     * - возвращён токен доступа
     */
    public function testGetTokenFree()
    {
        $provider = $this->providerRepository->findBySlug(ProviderFixture::TESTOMETRIKA);
        $requestParams = [
            'token' => $provider->getToken(),
            'user' => 'new-user',
            'service' => ServiceFixture::SERVICE_1,
            'payment_type' => PartnerApiController::PAYMENT_TYPE_EXTERNAL // <<<
        ];

        $token = $this->requestToken($requestParams);
        $this->assertTokenIsAccess($token);

        $this->assertTrustedPaymentCreated($provider, 'new-user');

        $tokenRepeat = $this->requestToken($requestParams);
        $this->assertTokenIsAccess($tokenRepeat);

        $this->assertNotEquals($token, $tokenRepeat, 'Tokens should be different every time');
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

    private function assertTokenIsPayment(string $token)
    {
        $this->assertNotNull($this->providerPaymentRepository->findByToken($token), 'Payment has to be persisted.');
    }

    private function assertTokenIsAccess(string $token)
    {
        $this->assertNotNull($this->accessRepository->findOneByToken($token), 'Access has to ve persisted.');
    }

    private function assertTrustedPaymentCreated(Provider $provider, string $userId)
    {
        $payment = $this->providerPaymentRepository->findOneByProviderAndUser($provider, $userId);
        $this->assertNotNull($payment);
        $this->assertEquals(new PaymentType(PaymentType::EXTERNAL), $payment->getType(), 'Payment should be marked as trusted.');
    }

    private function responseWithErrorText(string $string)
    {
        $content = json_decode($this->client->getResponse()->getContent());
        self::assertEquals($string, $content->detail);
    }
}