<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author: adavydov
 * @since: 20.10.2020
 */
#[ORM\Table]
#[ORM\Index(columns: ['uuid'])]
#[ORM\Entity(repositoryClass: 'App\Repository\ResultRepository')]
#[ORM\HasLifecycleCallbacks]
class Result
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var Test
     */
    #[ORM\ManyToOne(targetEntity: 'Test')]
    #[ORM\JoinColumn(name: 'test_id')]
    private $test;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 36)]
    private $uuid;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    private $data;

    /**
     * @var
     */
    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Test
     */
    public function getTest(): Test
    {
        return $this->test;
    }

    /**
     * @param Test $test
     */
    public function setTest(Test $test): void
    {
        $this->test = $test;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    public static function create(Test $test, string $uuid, string $data): Result
    {
        $result = new self();
        $result->setTest($test);
        $result->setUuid($uuid);
        $result->setData($data);
        return $result;
    }
}