<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\TestRepository')]
#[ORM\Index(columns: ['slug'])]
#[UniqueEntity('slug')]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'tests')]
    #[ORM\JoinColumn(name: 'catalog_id')]
    private ?Category $catalog = null;

    #[ORM\ManyToMany(targetEntity: \App\Entity\Service::class, mappedBy: 'tests')]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'test', targetEntity: \App\Entity\Question::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $questions;

    #[ORM\ManyToMany(targetEntity: \App\Entity\Author::class, inversedBy: 'tests')]
    private Collection $authors;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank]
    private ?string $slug = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'name_en', type: 'string', length: 255, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(name: 'description_en', type: 'text', nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: 'text')]
    private ?string $annotation = null;

    #[ORM\Column(name: 'annotation_en', type: 'text', nullable: true)]
    private ?string $annotationEn = null;

    #[ORM\Column(type: 'smallint')]
    private ?int $duration = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(name: 'active_en', type: 'boolean')]
    private bool $activeEn = false;

    #[ORM\Column(name: 'in_list', type: 'boolean')]
    private bool $inList = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isXmlSource = true;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $xmlFilename = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $calculatorName = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[\App\Validator\SourceName]
    private ?string $sourceName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $resultView = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $moderatorComment = null;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    public static function initDefault(): Test
    {
        $test = new Test();
        $test->setActive(0);
        $test->setActiveEn(0);
        return $test;
    }

    public function getId(): ?int
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

    public function getAnnotation(?string $locale = 'ru'): ?string
    {
        if ($locale === 'ru') {
            return $this->annotation;
        } elseif ($locale === 'en') {
            return $this->annotationEn ?? $this->annotation;
        }
        throw new \DomainException("Unsupported locale $locale.");
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

    public function isActive(?string $locale = 'ru'): bool
    {
        if ($locale === 'ru') {
            return $this->active;
        } elseif ($locale === 'en') {
            return $this->activeEn;
        }
        throw new \DomainException("Unsupported locale $locale.");
    }

    public function setActive(int $active): void
    {
        if (!in_array($active, [0, 1])) {
            throw new \InvalidArgumentException("Поле active может быть только в значении 0 и 1");
        }
        $this->active = $active == 1;
    }

    public function isActiveEn(): bool
    {
        return $this->activeEn == 1;
    }

    public function setActiveEn(int $activeEn): void
    {
        $this->activeEn = $activeEn == 1;
    }

    public function isXmlSource(): bool
    {
        return $this->isXmlSource;
    }

    public function setIsXmlSource(bool $isXmlSource): void
    {
        $this->isXmlSource = $isXmlSource;
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

    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }

    public function setSourceName(?string $value): void
    {
        $this->sourceName = $value;
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

    public function setAuthors(Collection $authors): void
    {
        $this->authors = $authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addTest($this);
        }
        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->removeElement($author)) {
            $author->removeTest($this);
        }
        return $this;
    }

    /**
     * @return Collection<Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function isInList(): bool
    {
        return $this->inList;
    }

    public function setInList(bool $inList): void
    {
        $this->inList = $inList;
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

    public function getModeratorComment(): ?string
    {
        return $this->moderatorComment;
    }

    public function setModeratorComment(?string $moderatorComment): void
    {
        $this->moderatorComment = $moderatorComment;
    }
}