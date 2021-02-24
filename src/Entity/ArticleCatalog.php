<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleCatalogRepository")
 * @ORM\Table(indexes={@ORM\Index(columns={"slug"})})
 * @Vich\Uploadable
 * @author: adavydov
 * @since: 24.02.2021
 */
class ArticleCatalog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, name="name_en")
     */
    private string $nameEn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $metaTitle;

    /**
     * @ORM\Column(type="string", length=255, name="meta_title_en")
     */
    private string $metaTitleEn;

    /**
     * @ORM\Column(type="text", length=355)
     */
    private string $metaDescription;

    /**
     * @ORM\Column(type="text", length=355, name="meta_description_en")
     */
    private string $metaDescriptionEn;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Article", mappedBy="catalog")
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}