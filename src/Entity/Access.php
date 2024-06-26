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
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Access implements TokenableInterface
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var Service
     */
    #[ORM\ManyToOne(targetEntity: 'Service')]
    #[ORM\JoinColumn(name: 'service_id', nullable: false)]
    private $service;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 36)]
    private $token;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', name: 'used_at', nullable: true)]
    private $usedAt;

    public function getToken(): string
    {
        return $this->token;
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    public static function init(Service $service): self
    {
        $o = new self();
        $o->token = Uuid::v4();
        $o->service = $service;
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

    /**
     * @return Service
     */
    public function getService(): Service
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService(Service $service): void
    {
        $this->service = $service;
    }
}