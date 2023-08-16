<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestRepository"))
 * @ORM\Table(indexes={@ORM\Index(columns={"slug"})})
 * @UniqueEntity("slug")
 * @author: adavydov
 * @since: 20.10.2020
 */
class Test
{
    const CALCULATOR_AUTO = 'auto';

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
     * @ORM\OneToMany(targetEntity="App\Entity\Analysis", mappedBy="test", cascade={"all"}, orphanRemoval=true)
     */
    private $analyses;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
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
     * @ORM\Column(type="boolean")
     */
    private bool $isXmlSource = true;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @var string|null
     */
    private ?string $xmlFilename = null;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $calculator = self::CALCULATOR_AUTO;

    /**
     * Custom calculator name
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $calculatorName = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $resultView = null;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->analyses = new ArrayCollection();
    }

    public static function initDefault(): Test
    {
        $test = new Test();
        $test->setActive(0);
        $test->setActiveEn(0);
        return $test;
    }

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

    public function setCatalog(Category $catalog): void
    {
        $this->catalog = $catalog;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getName(?string $locale = 'ru'): string
    {
        if ($locale === 'ru') {
            return $this->name;
        } elseif ($locale === 'en') {
            return $this->nameEn ?? $this->name;
        }
        throw new \DomainException("Unsupported locale $locale.");
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNameEn()
    {
        return $this->nameEn;
    }

    public function setNameEn($nameEn): void
    {
        $this->nameEn = $nameEn;
    }

    public function getDescription(?string $locale = 'ru'): ?string
    {
        if ($locale === 'ru') {
            return $this->description;
        } elseif ($locale === 'en') {
            return $this->descriptionEn ?? $this->description;
        }
        throw new \DomainException("Unsupported locale $locale.");
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn($descriptionEn): void
    {
        $this->descriptionEn = $descriptionEn;
    }

    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    public function setAnnotation($annotation): void
    {
        $this->annotation = $annotation;
    }

    public function getAnnotationEn(): ?string
    {
        return $this->annotationEn;
    }

    public function setAnnotationEn($annotationEn): void
    {
        $this->annotationEn = $annotationEn;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function isActive(): bool
    {
        return $this->active == 1;
    }

    public function setActive(int $active): void
    {
        if (!in_array($active, [0, 1])) {
            throw new \InvalidArgumentException("Поле active может быть только в значении 0 и 1");
        }
        $this->active = $active;
    }

    public function isActiveEn(): bool
    {
        return $this->activeEn == 1;
    }

    public function setActiveEn(int $activeEn): void
    {
        $this->activeEn = $activeEn;
    }

    public function getXmlFilename(): ?string
    {
        return $this->xmlFilename;
    }

    public function setXmlFilename($xmlFilename): void
    {
        $this->xmlFilename = $xmlFilename;
    }

    public function getCalculatorName(): ?string
    {
        return $this->calculatorName;
    }

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
     * @return ArrayCollection|PersistentCollection
     */
    public function getServices()
    {
        return $this->services;
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

    public function addQuestion(Question $question): Test
    {
        $question->setTest($this);
        $this->questions->add($question);
        return $this;
    }

    public function removeQuestion(Question $question)
    {
        $this->questions->removeElement($question);
    }


    public function setAnalyses(Collection $analyses): void
    {
        $this->analyses = $analyses;
    }

    /**
     * @return Collection<Question>
     */
    public function getAnalyses()
    {
        return $this->analyses;
    }

    public function addAnalysis(Analysis $analysis): Test
    {
        $analysis->setTest($this);
        $this->analyses->add($analysis);
        return $this;
    }

    public function removeAnalysis(Analysis $analysis)
    {
        $this->analyses->removeElement($analysis);
    }

    public function isInList(): bool
    {
        return $this->inList;
    }

    public function setInList(bool $inList): void
    {
        $this->inList = $inList;
    }

    public function isXmlSource(): bool
    {
        return $this->isXmlSource;
    }

    public function setIsXmlSource(bool $isXmlSource): void
    {
        $this->isXmlSource = $isXmlSource;
    }

    public function hasResultView(): bool
    {
        return $this->getResultView() !== null;
    }

    public function getResultView(): ?string
    {
        return $this->resultView;
    }

    public function setResultView(?string $resultView): void
    {
        $this->resultView = $resultView;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function isFree(): bool
    {
        $testServices = $this->getServices();
        return $testServices->count() === 0;
    }
}