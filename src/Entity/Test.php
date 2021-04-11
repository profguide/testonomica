<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestRepository"))
 * @ORM\Table(indexes={@ORM\Index(columns={"slug"})})
 * @author: adavydov
 * @since: 20.10.2020
 */
class Test
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="tests")
     * @ORM\JoinColumn(name="catalog_id")
     */
    private $catalog;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", mappedBy="tests")
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="test", cascade={"all"}, orphanRemoval=true)
     */
    private $questions;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, name="name_en", nullable=true)
     * @var string
     */
    private $nameEn;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="text", name="description_en", nullable=true)
     * @var string
     */
    private $descriptionEn;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $annotation;

    /**
     * @ORM\Column(type="text", name="annotation_en", nullable=true)
     * @var string
     */
    private $annotationEn;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    private $duration;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $active = true;

    /**
     * @ORM\Column(type="boolean", name="active_en")
     * @var boolean
     */
    private $activeEn = false;

    /**
     * @ORM\Column(type="boolean", name="in_list")
     * @var boolean
     */
    private bool $inList = true;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @var string|null
     */
    private $xmlFilename;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @var string|null
     */
    private $calculatorName;

    public static function initDefault(): Test
    {
        $test = new Test();
        $test->setActive(0);
        $test->setActiveEn(0);
        return $test;
    }

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

//    /**
//     * @return int
//     */
//    public function getCatalogId(): int
//    {
//        return $this->catalogId;
//    }
//
//    /**
//     * @param int $catalogId
//     */
//    public function setCatalogId(int $catalogId): void
//    {
//        $this->catalogId = $catalogId;
//    }

    public function getCatalog(): ?Category
    {
        return $this->catalog;
    }

    /**
     * @param Category $catalog
     */
    public function setCatalog(Category $catalog): void
    {
        $this->catalog = $catalog;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
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

    /**
     * @return mixed
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * @param mixed $nameEn
     */
    public function setNameEn($nameEn): void
    {
        $this->nameEn = $nameEn;
    }

    /**
     * @return mixed
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    /**
     * @param mixed $descriptionEn
     */
    public function setDescriptionEn($descriptionEn): void
    {
        $this->descriptionEn = $descriptionEn;
    }

    /**
     * @return mixed
     */
    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    /**
     * @param mixed $annotation
     */
    public function setAnnotation($annotation): void
    {
        $this->annotation = $annotation;
    }

    /**
     * @return mixed
     */
    public function getAnnotationEn(): ?string
    {
        return $this->annotationEn;
    }

    /**
     * @param mixed $annotationEn
     */
    public function setAnnotationEn($annotationEn): void
    {
        $this->annotationEn = $annotationEn;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active == 1;
    }

    /**
     * @param int $active
     */
    public function setActive(int $active): void
    {
        if (!in_array($active, [0, 1])) {
            throw new \LogicException("Поле active может быть только в значении 0 и 1");
        }
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isActiveEn(): bool
    {
        return $this->activeEn == 1;
    }

    /**
     * @param int $activeEn
     */
    public function setActiveEn(int $activeEn): void
    {
        $this->activeEn = $activeEn;
    }

    /**
     * @return mixed
     */
    public function getXmlFilename(): ?string
    {
        return $this->xmlFilename;
    }

    /**
     * @param mixed $xmlFilename
     */
    public function setXmlFilename($xmlFilename): void
    {
        $this->xmlFilename = $xmlFilename;
    }

    /**
     * @return null|string
     */
    public function getCalculatorName(): ?string
    {
        return $this->calculatorName;
    }

    /**
     * @param null|string $calculatorName
     */
    public function setCalculatorName(?string $calculatorName): void
    {
        $this->calculatorName = $calculatorName;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getActiveEn(): ?bool
    {
        return $this->activeEn;
    }

    public function addService(Service $service)
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isInList(): bool
    {
        return $this->inList;
    }

    /**
     * @param bool $inList
     */
    public function setInList(bool $inList): void
    {
        $this->inList = $inList;
    }

    public function setQuestions(Collection $questions): void
    {
        $this->questions = $questions;
    }

    /**
     * @return Collection<Question>
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    public function addQuestion(Question $question)
    {
        $question->setTest($this);
        $this->questions->add($question);
        return $this;
    }

    public function removeQuestion(Question $question)
    {
        $this->questions->removeElement($question);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}