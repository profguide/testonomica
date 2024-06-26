<?php
/**
 * @author: adavydov
 * @since: 18.11.2020
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author: adavydov
 * @since: 9.11.2020
 */
#[ORM\Table]
#[ORM\Entity]
class Service
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: false)]
    private $name;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: false)]
    private $slug;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text', nullable: false)]
    private $description;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $sum;

    #[ORM\ManyToMany(targetEntity: 'App\Entity\Test', inversedBy: 'services')]
    private $tests;

    public function __construct(string $name, int $sum, string $slug, string $description)
    {
        $this->name = $name;
        $this->sum = $sum;
        $this->slug = $slug;
        $this->description = $description;
        $this->tests = new ArrayCollection();
    }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->addService($this);
        }
        return $this;
    }

    /**
     * @return Test[]|ArrayCollection
     */
    public function tests()
    {
        return $this->tests;
    }

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getSum()
    {
        return $this->sum;
    }
}