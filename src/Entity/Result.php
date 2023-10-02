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
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /*
     * todo
     *  использовать ID с типом UUID вместо старого поля 'uuid', который и не uuid вовсе.
     *  и возвращать только ID всем и всегда. Пока при поиске результата валидировать UUID,
     *  и если это не UUID, то искать по старому полю 'uuid'
     *  Старое поле 'uuid' удалить через 3 года.
     *
     * План перехода:
     * сделать new_id binary(16) nullable неуникальный (готово)
     * заполнить поле для старых записей (готово)
     * сделаь дамп таблицы
     * удалить ячейчку id и сделать alter new_id уникальный, not nullable, сделать полю генерацию
     */
    // introduced 30.09.2023
    // todo сделать автогенерацию и уникальность, когда старый id будет удалён
    //  если сделать сейчас первичным, то доктрина немного путается и пытается удалить auto_increment в id.
    //  2.10.2023
    //  я удалил id, сделал migration:diff и запустил миграцию.
    //  Шаги были такие: удалить старый foreign key на provider_user_result.result_id, поменять тип provider_user_result.result_id
    //  а затем добавить снова foreign key на provider_user_result.result_id
    //  и здесь произошла ошибка General error: 1215 Cannot add foreign key constraint
    //  при этом существуют другие места, где foreign binary(16) работает.
    //  я не разобрался, плюнул и пошёл делать другие дела.
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $newId;

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

    public function getNewId(): Uuid
    {
        return $this->newId;
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

    public static function createAutoKey(Test $test, Progress $progress, ProgressSerializer $serializer): Result
    {
        $result = new self();
        $result->setTest($test);
        $result->newId = Uuid::v4();
        $result->setUuid($result->newId->toBase58());
        $result->setData($serializer->serialize($progress));
        $result->setHash($progress->hashSum());
        return $result;
    }
}