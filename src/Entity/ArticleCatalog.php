<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author: adavydov
 * @since: 24.02.2021
 */
#[ORM\Table]
#[ORM\Index(columns: ['slug'])]
#[ORM\Entity(repositoryClass: 'App\Repository\ArticleCatalogRepository')]
class ArticleCatalog
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, name: 'name_en')]
    private string $nameEn;

    #[ORM\Column(type: 'string', length: 255)]
    private string $metaTitle;

    #[ORM\Column(type: 'string', length: 255, name: 'meta_title_en')]
    private string $metaTitleEn;

    #[ORM\Column(type: 'text', length: 355)]
    private string $metaDescription;

    #[ORM\Column(type: 'text', length: 355, name: 'meta_description_en')]
    private string $metaDescriptionEn;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: 'Article', mappedBy: 'catalog')]
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}