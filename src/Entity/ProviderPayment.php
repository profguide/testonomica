<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use App\Payment\TokenableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"token"}),
 *     @ORM\Index(columns={"provider_id", "user"}),
 * })
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
     * @ORM\JoinColumn(name="payment_id")
     */
    private $payment;

    /**
     * @ORM\Column(type="string", length=32)
     * @var string
     */
    private $token;

    /**
     * @var Provider
     * @ORM\OneToOne(targetEntity="Provider")
     * @ORM\JoinColumn(name="provider_id")
     */
    private $provider;

    /**
     * @ORM\Column(type="string", length=255)
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

    public static function init(Provider $provider, string $user)
    {
        $providerPayment = new self();
        $providerPayment->setProvider($provider);
        $providerPayment->setUser($user);
        return $providerPayment;
    }
}