<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use App\V2\Provider\Policy\Payment\PaymentPolicy;
use App\V2\Provider\Policy\Test\TestPolicy;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author: adavydov
 * @since: 9.11.2020
 */
#[ORM\Table]
#[ORM\Entity]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $slug = null;

    #[ORM\Column(type: 'string', length: 32)]
    private ?string $token = null;

    /**
     * Политика оплаты
     */
    #[ORM\Column(type: 'string', length: 50, enumType: PaymentPolicy::class)]
    private PaymentPolicy $paymentPolicy = PaymentPolicy::PRE;

    /**
     * Политика тестов или какие тесты доступны для сохранения
     */
    #[ORM\Column(type: 'string', length: 50, enumType: TestPolicy::class)]
    private TestPolicy $testPolicy = TestPolicy::ONE_PROFTEST;

    /**
     * Счётчик выданных доступов
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $accessCount = 0;

    /**
     * Лимит доступов
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $accessLimit = 0;

    // todo test
    public static function create(
        string        $name,
        string        $slug,
        string        $token,
        PaymentPolicy $paymentPolicy,
        TestPolicy    $testPolicy,
        int           $accessLimit,
        int           $accessCount,
    ): self
    {
        $provider = new self();
        $provider->name = $name;
        $provider->slug = $slug;
        $provider->token = $token;
        $provider->paymentPolicy = $paymentPolicy;
        $provider->testPolicy = $testPolicy;
        $provider->accessLimit = $accessLimit;
        $provider->accessCount = $accessCount;

        return $provider;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getPaymentPolicy(): PaymentPolicy
    {
        return $this->paymentPolicy;
    }

    public function setPaymentPolicy(PaymentPolicy $paymentPolicy): void
    {
        $this->paymentPolicy = $paymentPolicy;
    }

    public function getTestPolicy(): TestPolicy
    {
        return $this->testPolicy;
    }

    public function setTestPolicy(TestPolicy $testPolicy): void
    {
        $this->testPolicy = $testPolicy;
    }

    public function getAccessCount(): int
    {
        return $this->accessCount;
    }

    public function setAccessCount(int $accessCount): void
    {
        $this->accessCount = $accessCount;
    }

    public function getAccessLimit(): int
    {
        return $this->accessLimit;
    }

    public function setAccessLimit(int $accessLimit): void
    {
        $this->accessLimit = $accessLimit;
    }

    public function addUser(ProviderUser $user): void
    {
        $this->accessCount += 1;
    }
}