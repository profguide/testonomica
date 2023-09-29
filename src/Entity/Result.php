<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Entity;

use App\Test\Progress\Progress;
use App\Test\Progress\ProgressSerializer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

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
    // todo change id to uuid (что делать со старыми?)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: 'Test')]
    #[ORM\JoinColumn(name: 'test_id')]
    private $test;

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

    public function getId(): int
    {
        return $this->id;
    }

    public function getTest(): Test
    {
        return $this->test;
    }

    public function setTest(Test $test): void
    {
        $this->test = $test;
    }

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

    /*
     * todo однажды
     *  использовать ID с типом UUID вместо старого поля 'uuid', который не uuid вовсе.
     *  и возвращать только ID всем и всегда. От старого поля 'uuid' полностью отойти (дать 3 года времени).
     *  а пока при поиске результата валидировать UUID, и если это не UUID, то искать по старому полю 'uuid'
     */
    public static function createAutoKey(Test $test, Progress $progress, ProgressSerializer $serializer): Result
    {
        $result = new self();
        $result->setTest($test);
        $result->setUuid(Uuid::v4()->toBase58());
        $result->setData($serializer->serialize($progress));
        $result->setHash($progress->hashSum());
        return $result;
    }
}