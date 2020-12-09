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
 * @ORM\Entity
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"token"}),
 *     @ORM\Index(columns={"provider_id", "user"}),
 * })
 * @ORM\HasLifecycleCallbacks()
 * @author: adavydov
 * @since: 9.11.2020
 */
class ProviderPayment implements TokenableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @var Payment
     * @ORM\OneToOne(targetEntity="Payment")
     * @ORM\JoinColumn(name="payment_id", nullable=false)
     */
    private $payment;

    /**
     * @ORM\Column(type="string", length=36, nullable=false)
     * @var string
     */
    private $token;

    /**
     * @var Provider
     * @ORM\ManyToOne(targetEntity="Provider")
     * @ORM\JoinColumn(name="provider_id", nullable=false)
     */
    private $provider;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $user;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return Provider
     */
    public function getProvider(): Provider
    {
        return $this->provider;
    }

    /**
     * @param Provider $provider
     */
    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public static function init(Payment $payment, Provider $provider, string $user)
    {
        $providerPayment = new self();
        $providerPayment->payment = $payment;
        $providerPayment->provider = $provider;
        $providerPayment->user = $user;
        $providerPayment->token = Uuid::v4();
        return $providerPayment;
    }
}