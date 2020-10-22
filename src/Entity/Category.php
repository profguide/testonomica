<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
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
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $pic;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $active;

//    public function __construct(string $slug, string $name)
//    {
//        $this->slug = $slug;
//        $this->name = $name;
//        $this->active = 1;
//    }

    /**
     * @return int
     */
    public function getId(): int
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
    public function getPic(): string
    {
        return $this->pic;
    }

    /**
     * @param string $pic
     */
    public function setPic(string $pic): void
    {
        $this->pic = $pic;
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
}