<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @author: adavydov
 * @since: 24.02.2021
 */
#[ORM\Table]
#[ORM\Index(columns: ['slug'])]
#[ORM\Entity(repositoryClass: 'App\Repository\ArticleRepository')]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: 'ArticleCatalog', inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'catalog_id')]
    private $catalog;

    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'name_en', type: 'string', length: 255)]
    private string $nameEn;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $subtitle;

    #[ORM\Column(name: 'subtitle_en', type: 'string', length: 255, nullable: true)]
    private string $subtitleEn;

    #[ORM\Column(type: 'text', length: 500, nullable: true)]
    private ?string $annotation = null;

    #[ORM\Column(name: 'annotation_en', type: 'text', length: 500, nullable: true)]
    private ?string $annotation_en = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $metaTitle;

    #[ORM\Column(name: 'meta_title_en', type: 'string', length: 255)]
    private string $metaTitleEn;

    #[ORM\Column(type: 'text', length: 355)]
    private string $metaDescription;

    #[ORM\Column(type: 'text', length: 355, name: 'meta_description_en')]
    private string $metaDescriptionEn;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'text', name: 'content_en')]
    private string $contentEn;

    #[ORM\OneToOne(targetEntity: 'Test')]
    #[ORM\JoinColumn(name: 'test_id', nullable: true)]
    private Test $test;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(type: 'boolean', name: 'active_en')]
    private bool $activeEn = false;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', name: 'updated_at')]
    private \DateTime $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $img = null;

    private ?File $imgFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imgWide = null;

    private ?File $imgWideFile = null;

    /**
     * @return string
     */
    public function getImg(): ?string
    {
        return $this->img;
    }

    /**
     * @param string $img
     */
    public function setImg(?string $img): void
    {
        $this->img = $img;
    }

    /**
     * @return File
     */
    public function getImgFile()
    {
        return $this->imgFile;
    }

    /**
     * @param File $imgFile
     */
    public function setImgFile($imgFile)
    {
        $this->imgFile = $imgFile;
        if ($imgFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return string
     */
    public function getImgWide(): ?string
    {
        return $this->imgWide;
    }

    /**
     * @param string $imgWide
     */
    public function setImgWide(string $imgWide): void
    {
        $this->imgWide = $imgWide;
    }

    /**
     * @return File
     */
    public function getImgWideFile()
    {
        return $this->imgWideFile;
    }

    /**
     * @param File $imgWideFile
     */
    public function setImgWideFile($imgWideFile): void
    {
        $this->imgWideFile = $imgWideFile;
        if ($imgWideFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }


    /**
     * @return int
     */
    public function getId(): ?int
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
     * @return ArticleCatalog
     */
    public function getCatalog(): ?ArticleCatalog
    {
        return $this->catalog;
    }

    /**
     * @param ArticleCatalog $catalog
     */
    public function setCatalog(ArticleCatalog $catalog): void
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
     * @return string
     */
    public function getNameEn(): string
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

    /**
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return string
     */
    public function getSubtitleEn(): string
    {
        return $this->subtitleEn;
    }

    /**
     * @param string $subtitleEn
     */
    public function setSubtitleEn(string $subtitleEn): void
    {
        $this->subtitleEn = $subtitleEn;
    }

    /**
     * @return string
     */
    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     */
    public function setMetaTitle(string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return string
     */
    public function getMetaTitleEn(): string
    {
        return $this->metaTitleEn;
    }

    /**
     * @param string $metaTitleEn
     */
    public function setMetaTitleEn(string $metaTitleEn): void
    {
        $this->metaTitleEn = $metaTitleEn;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription(string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getMetaDescriptionEn(): string
    {
        return $this->metaDescriptionEn;
    }

    /**
     * @param string $metaDescriptionEn
     */
    public function setMetaDescriptionEn(string $metaDescriptionEn): void
    {
        $this->metaDescriptionEn = $metaDescriptionEn;
    }

    /**
     * @return string
     */
    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    /**
     * @param string $annotation
     */
    public function setAnnotation(string $annotation): void
    {
        $this->annotation = $annotation;
    }

    /**
     * @return string
     */
    public function getAnnotationEn(): ?string
    {
        return $this->annotation_en;
    }

    /**
     * @param string $annotation_en
     */
    public function setAnnotationEn(string $annotation_en): void
    {
        $this->annotation_en = $annotation_en;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContentEn(): string
    {
        return $this->contentEn;
    }

    /**
     * @param string $contentEn
     */
    public function setContentEn(string $contentEn): void
    {
        $this->contentEn = $contentEn;
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isActiveEn(): bool
    {
        return $this->activeEn;
    }

    /**
     * @param bool $activeEn
     */
    public function setActiveEn(bool $activeEn): void
    {
        $this->activeEn = $activeEn;
    }

    private function setCreatedAt(\DateTime $value)
    {
        $this->createdAt = $value;
    }

    private function setUpdatedAt(\DateTime $value)
    {
        $this->updatedAt = $value;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}