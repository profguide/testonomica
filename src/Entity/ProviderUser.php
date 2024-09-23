<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table]
#[ORM\Entity(repositoryClass: 'App\Repository\ProviderUserRepository')]
#[ORM\HasLifecycleCallbacks]
class ProviderUser
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\ManyToOne(targetEntity: 'Provider', inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'provider_id', nullable: false)]
    private ?Provider $provider = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $extUserId = null; // todo store hash instead of direct value.

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    // todo test
    public static function create(Provider $provider, mixed $extUserId): self
    {
        $user = new self();
        $user->provider = $provider;
        $user->extUserId = $extUserId;

        return $user;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }

    public function getExtUserId(): ?string
    {
        return $this->extUserId;
    }

    public function setExtUserId(string $extUserId): void
    {
        $this->extUserId = $extUserId;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    private function setCreatedAt(\DateTime $value): void
    {
        $this->createdAt = $value;
    }

    public function __toString(): string
    {
        return $this->id->toBinary();
    }
}