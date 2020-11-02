<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResultRepository")
 * @ORM\HasLifecycleCallbacks()
 * @author: adavydov
 * @since: 20.10.2020
 */
class Result
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @var Test
     * @ORM\ManyToOne(targetEntity="Test")
     * @ORM\JoinColumn(name="test_id")
     */
    private $test;

    /**
     * @ORM\Column(type="string", length=36)
     * @var string
     */
    private $uuid;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $data;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @var
     */
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

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    public static function create(Test $test, string $uuid, string $data)
    {
        $result = new self();
        $result->setTest($test);
        $result->setUuid($uuid);
        $result->setData($data);
        return $result;
    }
}