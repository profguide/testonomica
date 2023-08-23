<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

/**
 * @author: adavydov
 * @since: 24.02.2021
 */
#[ORM\Table]
#[ORM\Index(columns: ['slug'])]
#[ORM\Entity(repositoryClass: 'App\Repository\ArticleRepository')]
#[ORM\HasLifecycleCallbacks]
#[Uploadable]
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

    #[ORM\Column(name: 'meta_description_en', type: 'text', length: 355)]
    private string $metaDescriptionEn;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(name: 'content_en', type: 'text')]
    private string $contentEn;

    #[ORM\OneToOne(targetEntity: 'Test')]
    #[ORM\JoinColumn(name: 'test_id', nullable: true)]
    private ?Test $test = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(name: 'active_en', type: 'boolean')]
    private bool $activeEn = false;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTime $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $img = null;

    #[UploadableField(mapping: 'articles', fileNameProperty: 'img')]
    private ?File $imgFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imgWide = null;

    #[UploadableField(mapping: 'articles', fileNameProperty: 'imgWide')]
    private ?File $imgWideFile = null;

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): void
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
        if ($imgFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImgWide(): ?string
    {
        return $this->imgWide;
    }

    public function setImgWide(?string $imgWide): void
    {
        $this->imgWide = $imgWide;
    }

    public function getImgWideFile(): ?File
    {
        return $this->imgWideFile;
    }

    public function setImgWideFile(?File $imgWideFile): void
    {
        $this->imgWideFile = $imgWideFile;
        if ($imgWideFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCatalog(): ?ArticleCatalog
    {
        return $this->catalog;
    }

    public function setCatalog(ArticleCatalog $catalog): void
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

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): void
    {
        $this->nameEn = $nameEn;
    }

    public function getSubtitle(?string $locale = 'ru'): string
    {
        if ($locale === 'ru') {
            return $this->subtitle;
        } elseif ($locale === 'en') {
            return $this->subtitleEn ?? $this->subtitle;
        }
        throw new \DomainException("Unsupported locale $locale.");
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getSubtitleEn(): string
    {
        return $this->subtitleEn;
    }

    public function setSubtitleEn(string $subtitleEn): void
    {
        $this->subtitleEn = $subtitleEn;
    }

    public function getMetaTitle(?string $locale = 'ru'): string
    {
        if ($locale === 'ru') {
            return $this->metaTitle;
        } elseif ($locale === 'en') {
            return $this->metaTitleEn ?? $this->metaTitle;
        }
        throw new \DomainException("Unsupported locale $locale.");
    }

    public function setMetaTitle(string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaTitleEn(): string
    {
        return $this->metaTitleEn;
    }

    public function setMetaTitleEn(string $metaTitleEn): void
    {
        $this->metaTitleEn = $metaTitleEn;
    }

    public function getMetaDescription(?string $locale = 'ru'): string
    {
        if ($locale === 'ru') {
            return $this->metaDescription;
        } elseif ($locale === 'en') {
            return $this->metaDescriptionEn ?? $this->metaDescription;
        }
        throw new \DomainException("Unsupported locale $locale.");
    }

    public function setMetaDescription(string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getMetaDescriptionEn(): string
    {
        return $this->metaDescriptionEn;
    }

    public function setMetaDescriptionEn(string $metaDescriptionEn): void
    {
        $this->metaDescriptionEn = $metaDescriptionEn;
    }

    public function getAnnotation(?string $locale = 'ru'): ?string
    {
        if ($locale === 'ru') {
            return $this->annotation;
        } elseif ($locale === 'en') {
            return $this->annotation_en ?? $this->annotation;
        }
        throw new \DomainException("Unsupported locale $locale.");
    }

    public function setAnnotation(string $annotation): void
    {
        $this->annotation = $annotation;
    }

    public function getAnnotationEn(): ?string
    {
        return $this->annotation_en;
    }

    public function setAnnotationEn(string $annotation_en): void
    {
        $this->annotation_en = $annotation_en;
    }

    public function getContent(?string $locale = 'ru'): string
    {
        if ($locale === 'ru') {
            return $this->content;
        } elseif ($locale === 'en') {
            return $this->contentEn ?? $this->content;
        }
        throw new \DomainException("Unsupported locale $locale.");
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContentEn(): string
    {
        return $this->contentEn;
    }

    public function setContentEn(string $contentEn): void
    {
        $this->contentEn = $contentEn;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): void
    {
        $this->test = $test;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function isActiveEn(): bool
    {
        return $this->activeEn;
    }

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