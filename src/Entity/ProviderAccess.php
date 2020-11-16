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
 * @ORM\Table(indexes={@ORM\Index(columns={"token"})})
 * @ORM\HasLifecycleCallbacks()
 * @author: adavydov
 * @since: 9.11.2020
 */
class ProviderAccess implements TokenableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=36)
     * @var string
     */
    private $token;

    /**
     * @var Provider
     * @ORM\ManyToOne(targetEntity="Provider")
     * @ORM\JoinColumn(name="provider_id")
     */
    private $provider;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="used_at", nullable=true)
     */
    private $usedAt;

    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    public static function init(Provider $provider): self
    {
        $o = new self();
        $o->provider = $provider;
        $o->token = Uuid::v4();
        return $o;
    }

    public function isUsed(): bool
    {
        return !empty($this->usedAt);
    }

    public function setUsed(): void
    {
        $this->usedAt = new \DateTime();
    }

    public function setProvider(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }
}