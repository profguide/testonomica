<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OptionRepository"))
 * @ORM\Table(indexes={@ORM\Index(columns={"question"})})
 * @ORM\HasLifecycleCallbacks
 * @author: adavydov
 * @since: 09.04.2021
 */
class QuestionItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="items")
     * @ORM\JoinColumn(name="question")
     */
    private Question $question;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $value;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $correct;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $text;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $textEn;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     */
    private \DateTime $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img = null;

    /**
     * @Vich\UploadableField(mapping="thumbnails", fileNameProperty="img")
     */
    private ?File $imgFile = null;

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
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->correct;
    }

    /**
     * @param bool $correct
     */
    public function setCorrect(bool $correct): void
    {
        $this->correct = $correct;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTextEn(): string
    {
        return $this->textEn;
    }

    /**
     * @param string $textEn
     */
    public function setTextEn(string $textEn): void
    {
        $this->textEn = $textEn;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return null
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param null $img
     */
    public function setImg($img): void
    {
        $this->img = $img;
    }

    /**
     * @return File|null
     */
    public function getImgFile(): ?File
    {
        return $this->imgFile;
    }

    /**
     * @param File|null $imgFile
     */
    public function setImgFile(?File $imgFile): void
    {
        $this->imgFile = $imgFile;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }

//    public static function initDefault(): Test
//    {
//        $test = new Test();
//        $test->setActive(0);
//        $test->setActiveEn(0);
//        return $test;
//    }
//
//    public function __construct()
//    {
//        $this->services = new ArrayCollection();
//    }
}