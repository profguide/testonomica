<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\Table(indexes={@ORM\Index(columns={"slug"})})
 * @author: adavydov
 * @since: 20.10.2020
 */
class Category
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $pic;

    /**
     * @var UploadedFile
     */
    private $picFile;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $active;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     * @var
     */
    private $updatedAt;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Test", mappedBy="catalog")
     */
    private $tests;

    public function __construct()
    {
        $this->tests = new ArrayCollection();
        $this->updatedAt = new \DateTime('now');
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
     * @param string|null $locale
     * @return string
     */
    public function getName(?string $locale = 'ru'): string
    {
        if ($locale === 'ru') {
            return $this->name;
        } elseif ($locale === 'en') {
            return $this->nameEn ?? $this->name;
        }
        throw new \DomainException("Unsupported locale $locale.");
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
    public function getPic(): ?string
    {
        return $this->pic;
    }

    /**
     * @param string $pic
     */
    public function setPic(?string $pic): void
    {
        $this->pic = $pic;
    }

    /**
     * @return UploadedFile
     */
    public function getPicFile()
    {
        return $this->picFile;
    }

    /**
     * @param UploadedFile $picFile
     */
    public function setPicFile($picFile): void
    {
        $this->picFile = $picFile;
        //otherwise the event listeners won't be called and the file is lost
        if ($picFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function setActive(int $value): void
    {
        $this->active = $value;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = 1;
    }

    public function deActivate(): void
    {
        $this->active = 0;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @return Collection|Test[]
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->setCatalog($this);
        }

        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->contains($test)) {
            $this->tests->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getCatalog() === $this) {
                $test->setCatalog(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}