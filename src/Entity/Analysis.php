<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnalysisRepository")
 * @ORM\Table
 * @author: adavydov
 * @since: 15.04.2021
 */
class Analysis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Test", inversedBy="analyses")
     */
    private Test $test;

    /**
     * @ORM\OneToMany(targetEntity="AnalysisBlock", mappedBy="analysis", cascade={"all"}, orphanRemoval=true)
     */
    private $blocks;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $progressPercentVariableName = null;

    /**
     * Для отображения в прогресс-баре >7< из 12
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $progressVariableName = null;

    /**
     * Для отображения в прогресс-баре 7 из >12<
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $progressVariableMax = null;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getTest(): Test
    {
        return $this->test;
    }

    public function setTest(Test $test): void
    {
        $this->test = $test;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getProgressPercentVariableName(): ?string
    {
        return $this->progressPercentVariableName;
    }

    public function setProgressPercentVariableName(?string $progressPercentVariableName): void
    {
        $this->progressPercentVariableName = $progressPercentVariableName;
    }

    public function getProgressVariableName(): ?string
    {
        return $this->progressVariableName;
    }

    public function setProgressVariableName(?string $progressVariableName): void
    {
        $this->progressVariableName = $progressVariableName;
    }

    public function getProgressVariableMax(): ?int
    {
        return $this->progressVariableMax;
    }

    public function setProgressVariableMax(?int $progressVariableMax): void
    {
        $this->progressVariableMax = $progressVariableMax;
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

    public function setBlocks($blocks): void
    {
        $this->blocks = $blocks;
    }

    public function AddBlock(AnalysisBlock $block): Analysis
    {
        $block->setAnalysis($this);
        $this->blocks->add($block);
        return $this;
    }

    public function removeBlock(AnalysisBlock $block)
    {
        $this->blocks->removeElement($block);
    }
}