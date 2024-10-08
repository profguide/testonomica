<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Entity;

use App\Test\Progress\Progress;
use App\Test\Progress\ProgressSerializer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @author: adavydov
 * @since: 20.10.2020
 *
 * По хорошему эту структуру нужно назвать не Result, а Progress.
 */
#[ORM\Table]
#[ORM\Index(columns: ['uuid'])]
#[ORM\Entity(repositoryClass: 'App\Repository\ResultRepository')]
#[ORM\HasLifecycleCallbacks]
class Result implements \Stringable
{
    /*
     * todo
     *  использовать ID с типом UUID вместо старого поля 'uuid', который и не uuid вовсе.
     *  и возвращать только ID всем и всегда. Пока при поиске результата валидировать UUID,
     *  и если это не UUID, то искать по старому полю 'uuid'
     *  Старое поле 'uuid' удалить через 3 года.
     */
    // introduced 02.10.2023
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $newId;

    #[ORM\ManyToOne(targetEntity: 'Test')]
    #[ORM\JoinColumn(name: 'test_id')]
    private $test;

    /**
     * @deprecated, use primary key
     */
    #[ORM\Column(type: 'string', length: 36)]
    private $uuid;

    #[ORM\Column(type: 'text')]
    private $data;

    /**
     * Hash для поиска совпадений для анализа и пресекания повторного сохранения.
     * Однако наверняка возможны совпадения у разных людей, поэтому поле не уникально.
     * И пресекать повторное сохранение можно лишь для одного человека.
     */
    #[ORM\Column(type: 'string', unique: false)]
    private ?string $hash = null;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private $createdAt;

    public function getNewId(): Uuid
    {
        return $this->newId;
    }


    public function setNewId(Uuid $uuid): void
    {
        $this->newId = $uuid;
    }

    public function getTest(): Test
    {
        return $this->test;
    }

    public function setTest(Test $test): void
    {
        $this->test = $test;
    }

    /**
     * @deprecated, use getId (getNewId)
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    public static function createAutoKey(Test $test, Progress $progress, ProgressSerializer $serializer): Result
    {
        $result = new self();
        $result->setTest($test);
        $result->setUuid(Uuid::v4()->toBase58());
        $result->setData($serializer->serialize($progress));
        $result->setHash($progress->hashSum());
        return $result;
    }

    public function __toString()
    {
        return $this?->newId?->toBase58() ?? $this->uuid ?? "Result " . spl_object_id($this);
    }
}