<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestRepository"))
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
     * @ORM\Column(type="integer", name="catalog_id")
     * @var int
     */
    private $catalogId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @var Category
     */
    private $catalog;

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
     * @ORM\Column(type="string", length=255, name="name_en")
     * @var string
     */
    private $nameEn;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="text", name="description_en")
     * @var string
     */
    private $descriptionEn;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $annotation;

    /**
     * @ORM\Column(type="text", name="annotation_en")
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
    private $active;

    /**
     * @ORM\Column(type="boolean", name="active_en")
     * @var boolean
     */
    private $activeEn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $xmlFilename;

    public static function initDefault(): Test
    {
        $test = new Test();
        $test->setActive(0);
        $test->setActiveEn(0);
        return $test;
    }

//    public function __construct(int $catalogId, string $slug, string $name, int $duration)
//    {
//        $this->catalogId = $catalogId;
//        $this->slug = $slug;
//        $this->name = $name;
//        $this->duration = $duration;
//        $this->active = 0;
//        $this->activeEn = 0;
//    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCatalogId(): int
    {
        return $this->catalogId;
    }

    /**
     * @param int $catalogId
     */
    public function setCatalogId(int $catalogId): void
    {
        $this->catalogId = $catalogId;
    }

    public function getCatalog()
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
}