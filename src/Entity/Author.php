<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\Index(columns: ['slug'])]
#[UniqueEntity('slug')]
final class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: \App\Entity\Test::class, mappedBy: 'authors')]
    private Collection $tests;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $slug = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $nameEn = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $about = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $aboutEn = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(?string $nameEn): void
    {
        $this->nameEn = $nameEn;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): void
    {
        $this->about = $about;
    }

    public function getAboutEn(): ?string
    {
        return $this->aboutEn;
    }

    public function setAboutEn(?string $aboutEn): void
    {
        $this->aboutEn = $aboutEn;
    }

    /**
     * @return Collection<Test>
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->addAuthor($this);
        }
        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->removeElement($test)) {
            $test->removeAuthor($this);
        }
        return $this;
    }

    public function setTests(Collection $tests): void
    {
        $this->tests = $tests;
    }
}