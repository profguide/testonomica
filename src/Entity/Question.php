<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository"))
 * @ORM\Table(indexes={@ORM\Index(columns={"test"})})
 * @ORM\HasLifecycleCallbacks
 * @author: adavydov
 * @since: 09.04.2021
 */
class Question
{
    // классический вопрос - один возможный вариант радиокнопка
    const TYPE_OPTION = 'option';
    // несколько доступных вариантов
    const TYPE_CHECKBOX = "checkbox";
    // вопрос с ответом в виде текста, который нужно ввести
    const TYPE_TEXT = "text";
    // список ответов, которые нужно прокликать в порядке от самого важного до наименнее важного, или наоборот.
    const TYPE_RATING = 'rating';
    // диапазон значений (Гэлап, СС) - есть диапазов ответов, -2 -1 0 +1 +2, но в ответах нет неправильных значений.
    const TYPE_GRADIENT = 'gradient';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Test", inversedBy="questions")
     * @ORM\JoinColumn(name="test")
     * @Ignore
     */
    private Test $test;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuestionItem", mappedBy="question", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $items;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private string $type = self::TYPE_OPTION;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Ignore
     */
    private ?string $nameEn = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Ignore
     */
    private ?string $textEn = null;

    /**
     * Ex-group
     * @ORM\Column(type="string", nullable=true)
     * @Ignore
     */
    private ?string $variety = null;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     * @Ignore
     */
    private \DateTime $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img = null;

    /**
     * @Vich\UploadableField(mapping="thumbnails", fileNameProperty="img")
     * @Ignore
     */
    private ?File $imgFile = null;

    /**
     * Нужное/максимальное количество ответов, которые можно выбрать.
     * Используется в вопросах типа checkbox, rating.
     */
    private $count;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * Диапазон сгенерированных ответов.
     * Может быть сспользовано в вопросах типа gradient.
     */
    private $range;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private int $timer = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $wrong;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     * @Ignore
     */
    private $wrongEn;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $correct;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     * @Ignore
     */
    private $correctEn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabledBack = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabledForward = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $showAnswer = false;

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        if (!in_array($type, [self::TYPE_CHECKBOX, self::TYPE_OPTION, self::TYPE_RATING, self::TYPE_TEXT, self::TYPE_GRADIENT])) {
            throw new \InvalidArgumentException("Invalid type name: {$type}.");
        }
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    /**
     * @param string $nameEn
     */
    public function setNameEn(string $nameEn): void
    {
        $this->nameEn = $nameEn;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getTextEn(): ?string
    {
        return $this->textEn;
    }

    public function setTextEn(?string $textEn): void
    {
        $this->textEn = $textEn;
    }

    public function getVariety(): string
    {
        return $this->variety;
    }

    public function setVariety(?string $variety): void
    {
        $this->variety = $variety;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getImg()
    {
        return $this->img;
    }

    public function setImg($img): void
    {
        $this->img = $img;
    }

    public function getImgFile(): ?File
    {
        return $this->imgFile;
    }

    public function setImgFile(?File $imgFile): void
    {
        $this->imgFile = $imgFile;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count): void
    {
        $this->count = $count;
    }

    public function getRange()
    {
        return $this->range;
    }

    public function setRange($range): void
    {
        $this->range = $range;
    }

    public function getTimer(): int
    {
        return $this->timer;
    }

    public function setTimer(int $timer): void
    {
        $this->timer = $timer;
    }

    public function getWrong(): ?string
    {
        return $this->wrong;
    }

    public function setWrong(string $wrong): void
    {
        $this->wrong = $wrong;
    }

    public function getWrongEn(): ?string
    {
        return $this->wrongEn;
    }

    public function setWrongEn(string $wrongEn): void
    {
        $this->wrongEn = $wrongEn;
    }

    public function getCorrect(): ?string
    {
        return $this->correct;
    }

    public function setCorrect(string $correct): void
    {
        $this->correct = $correct;
    }

    public function getCorrectEn(): ?string
    {
        return $this->correctEn;
    }

    public function setCorrectEn(string $correctEn): void
    {
        $this->correctEn = $correctEn;
    }

    public function isEnabledBack(): bool
    {
        return $this->enabledBack;
    }

    public function setEnabledBack(bool $enabledBack): void
    {
        $this->enabledBack = $enabledBack;
    }

    public function isEnabledForward(): bool
    {
        return $this->enabledForward;
    }

    public function setEnabledForward(bool $enabledForward): void
    {
        $this->enabledForward = $enabledForward;
    }

    public function isShowAnswer(): bool
    {
        return $this->showAnswer;
    }

    public function setShowAnswer(bool $showAnswer): void
    {
        $this->showAnswer = $showAnswer;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    /**
     * @return Collection<QuestionItem>
     */
    public function getItems()
    {
        return $this->items;
    }

    public function addItem(QuestionItem $item)
    {
        $item->setQuestion($this);
        $this->items->add($item);
        return $this;
    }

    public function removeItem(QuestionItem $item)
    {
        $this->items->removeElement($item);
    }

    public function hasCorrectValues(): bool
    {
        if ($this->type == self::TYPE_TEXT) {
            return true;
        } elseif ($this->type == self::TYPE_OPTION || $this->type == self::TYPE_CHECKBOX) {
            // todo think of how to determine if question has correct values so as not to overload DB
            //  may be add field: hasCorrectValue? and make it auto counting on the saving stage?
            /**@var QuestionItem $item */
            foreach ($this->getItems() as $item) {
                if ($item->isCorrect()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @Ignore
     */
    public function getCorrectValues(): array
    {
        $values = [];
        /**@var QuestionItem $item */
        foreach ($this->getItems() as $item) {
            if ($this->type == self::TYPE_TEXT) {
                $values[] = $item->getValue();
            } elseif ($this->type == self::TYPE_OPTION || $this->type == self::TYPE_CHECKBOX) {
                if ($item->isCorrect()) {
                    $values[] = $item->getValue();
                }
            } else {
                throw new \RuntimeException("This type does not support correct values type: \"{$this->type}\".");
            }
        }
        return $values;
    }

    /**
     * Counts maximum value of items.
     * If there is at least one "correct", their count will be returned.
     *
     * @return int
     */
    public function maxValue(): int
    {
        $valuesSum = 0;
        $correctSum = 0;
        if ($this->type !== self::TYPE_TEXT) {
            /**@var QuestionItem $item */
            foreach ($this->getItems() as $item) {
                $correctSum += $item->isCorrect() ? 1 : 0;
                $value = (int)$item->getValue();
                if ($valuesSum < $value) {
                    $valuesSum = $value;
                }
            }
        }
        if ($correctSum > 0) {
            return $correctSum;
        } else {
            return $valuesSum;
        }
    }
}