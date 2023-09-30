<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table]
#[ORM\Entity(repositoryClass: 'App\Repository\ProviderUsersResultsRepository')]
#[ORM\HasLifecycleCallbacks]
class ProviderUserResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'ProviderUser', inversedBy: 'results')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?ProviderUser $user = null;

    #[ORM\ManyToOne(targetEntity: 'Result')]
    #[ORM\JoinColumn(name: 'result_id', nullable: false)]
    private ?Result $result = null;

    #[ORM\ManyToOne(targetEntity: 'Test')]
    #[ORM\JoinColumn(name: 'test_id', nullable: false)]
    private ?Test $test = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    public static function create(ProviderUser $user, Result $result, Test $test): self
    {
        $entity = new self();
        $entity->user = $user;
        $entity->result = $result;
        $entity->test = $test;

        return $entity;
    }

    public function getUser(): ProviderUser
    {
        return $this->user;
    }

    public function setUser(ProviderUser $user): void
    {
        $this->user = $user;
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function setResult(Result $result): void
    {
        $this->result = $result;
    }

    public function getTest(): Test
    {
        return $this->test;
    }

    public function setTest(Test $test): void
    {
        $this->test = $test;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    private function setCreatedAt(\DateTime $value): void
    {
        $this->createdAt = $value;
    }
}