<?php

declare(strict_types=1);

namespace App\Tests\V2\Provider\Command\RegisterUser;

use App\Entity\Provider;
use App\Entity\ProviderUser;
use App\Repository\ProviderRepository;
use App\V2\Provider\Command\RegisterUser\RegisterProviderUser;
use App\V2\Provider\Command\RegisterUser\RegisterProviderUserHandler;
use App\V2\Provider\Policy\Payment\PaymentPolicy;
use App\V2\Provider\Policy\Payment\Validator\Exception\PaymentPolicyValidationException;
use App\V2\Provider\Policy\Test\LicensePolicy;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RegisterProviderUserTest extends KernelTestCase
{
    private ?ProviderRepository $providerRepository;
    private ?RegisterProviderUserHandler $handler;
    private ?EntityManagerInterface $entityManager;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->providerRepository = $container->get(ProviderRepository::class);
        $this->handler = $container->get(RegisterProviderUserHandler::class);

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Отменяем транзакцию после каждого теста
        $this->entityManager->rollback();
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * Проверяет, что вместо создания нового пользователя возвращается старый
     *
     * Условия:
     * - старый пользователь
     *
     * Ожидаемый результат:
     * - новый пользователь не создан
     * - счётчик выданных доступов не увеличился
     */
    public function testUserAlreadyExists()
    {
        $provider = $this->createProvider(PaymentPolicy::POST, 10, 0);
        $this->createUser('555', $provider);

        $handler = $this->handler;
        $handler(new RegisterProviderUser($provider, '555'));

        $reloadedProvider = $this->providerRepository->findBySlug('umbrella');
        self::assertEquals(0, $reloadedProvider->getAccessCount()); // <<< counter did not change
    }

    /**
     * Проверяет, что пользователь создаётся и счётчик выданных доступов увеличивется на один,
     * когда установлена политика постоплаты
     *
     * Условия:
     * - новый пользователь
     * - установлена политкика постоплаты
     * - пользователь должен быть создан
     *
     * Ожидаемый результат:
     * - создан пользователь
     * - счётчик выданных доступов увеличился на 1
     */
    public function testCreateUserSuccessNoLimits()
    {
        $provider = $this->createProvider(PaymentPolicy::POST, 0, 0);

        $handler = $this->handler;
        $user = $handler(new RegisterProviderUser($provider, (string)mt_rand()));

        self::assertNotNull($user);
        self::assertNotNull($user->getId()); // means persist
        $reloadedProvider = $this->providerRepository->findBySlug('umbrella');
        self::assertEquals(1, $reloadedProvider->getAccessCount()); // <<< counter incremented by one
    }

    /**
     * Проверяет, что пользователь создаётся и счётчик выданных доступов увеличивается на один,
     * когда установлена политика предоплаты с недостигнутым пределом выданных доступов
     *
     * Условия:
     * - новый пользователь
     * - установлена политкика предоплаты
     * - лимит выданных доступов не исчерпан
     *
     * Ожидаемый результат:
     * - создан пользователь
     * - счётчик выданных доступов увеличился на 1
     */
    public function testUserCreationSuccessWithPrepaymentAndLimit()
    {
        $provider = $this->createProvider(PaymentPolicy::PRE, 10, 9);

        $handler = $this->handler;
        $user = $handler(new RegisterProviderUser($provider, (string)mt_rand()));

        self::assertNotNull($user);
        self::assertNotNull($user->getId()); // means persist
        $reloadedProvider = $this->providerRepository->findBySlug('umbrella');
        self::assertEquals(10, $reloadedProvider->getAccessCount()); // <<< counter incremented by one
    }

    /**
     * Проверяет, что происходит ошибка при достижении лимита выданных доступов,
     * когда установлена политика предоплаты с достигнутым лимитом выданных доступов
     *
     * Условия:
     * - новый пользователь
     * - установлена политкика предоплаты
     * - лимит выданных доступов исчерпан
     *
     * Ожидаемый результат:
     * - выброшено исключение
     */
    public function testPaymentPolicyErrorWhenLimitReached()
    {
        $provider = $this->createProvider(PaymentPolicy::PRE, 10, 10);

        $this->expectException(PaymentPolicyValidationException::class);

        $handler = $this->handler;
        $handler(new RegisterProviderUser($provider, (string)mt_rand()));
    }

    private function createProvider(PaymentPolicy $paymentPolicy, int $accessLimit, int $accessCount): Provider
    {
        $provider = Provider::create(
            'Umbrella',
            'umbrella',
            'umbrella',
            $paymentPolicy,
            LicensePolicy::ONE_PROFTEST,
            $accessLimit,
            $accessCount
        );

        $this->entityManager->persist($provider);
        $this->entityManager->flush();

        return $provider;
    }

    private function createUser(string $extUserId, Provider $provider): void
    {
        $user = ProviderUser::create($provider, $extUserId);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}