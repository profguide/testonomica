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
class Access implements TokenableInterface
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

    public static function init(): self
    {
        $o = new self();
        $o->token = Uuid::v4();
        return $o;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function isUsed(): bool
    {
        return !empty($this->usedAt);
    }

    public function setUsed(): void
    {
        $this->usedAt = new \DateTime();
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }
}