<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use App\Payment\TokenableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @author: adavydov
 * @since: 9.11.2020
 */
#[ORM\Table]
#[ORM\Index(columns: ['token'])]
#[ORM\Index(columns: ['provider_id', 'user'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class ProviderPayment implements TokenableInterface
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var Payment
     */
    #[ORM\OneToOne(targetEntity: 'Payment')]
    #[ORM\JoinColumn(name: 'payment_id', nullable: false)]
    private $payment;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 36, nullable: false)]
    private $token;

    /**
     * @var Provider
     */
    #[ORM\ManyToOne(targetEntity: 'Provider')]
    #[ORM\JoinColumn(name: 'provider_id', nullable: false)]
    private $provider;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private $user;

    /**
     * Indicates whether the first access was granted after payment.
     * @var boolean
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $grantedAccess = false;

    /**
     * Payments might be local or external. external - provider is supposed to provide payment on their own.
     * @var int
     */
    #[ORM\Column(type: 'integer', length: 1, nullable: false, options: ['default' => 0])]
    private $type = PaymentType::DEFAULT;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $userId): void
    {
        $this->user = $userId;
    }

    public function isGrantedAccess(): bool
    {
        return $this->grantedAccess;
    }

    public function setGrantedAccess(): void
    {
        if ($this->grantedAccess) {
            throw new \DomainException('Access has been already granted.');
        }
        $this->grantedAccess = true;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): PaymentType
    {
        return new PaymentType($this->type);
    }

    public static function init(Payment $payment, Provider $provider, string $user, PaymentType $type): ProviderPayment
    {
        $providerPayment = new self();
        $providerPayment->payment = $payment;
        $providerPayment->provider = $provider;
        $providerPayment->user = $user;
        $providerPayment->token = Uuid::v4();
        $providerPayment->type = $type->value();
        return $providerPayment;
    }
}