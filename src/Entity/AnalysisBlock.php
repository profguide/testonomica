<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnalysisBlockRepository")
 * @ORM\Table
 * @author: adavydov
 * @since: 15.04.2021
 */
class AnalysisBlock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Analysis", inversedBy="blocks")
     */
    private Analysis $analysis;

    /**
     * @ORM\OneToMany(targetEntity="AnalysisCondition", mappedBy="block", cascade={"all"}, orphanRemoval=true)
     */
    private $conditions;

    /**
     * @ORM\Column(type="text")
     */
    private string $text = "";

    /**
     * @ORM\Column(type="text")
     */
    private string $textEn = "";

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getAnalysis(): Analysis
    {
        return $this->analysis;
    }

    public function setAnalysis(Analysis $analysis): void
    {
        $this->analysis = $analysis;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getTextEn(): string
    {
        return $this->textEn;
    }

    public function setTextEn(string $textEn): void
    {
        $this->textEn = $textEn;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function setConditions($conditions): void
    {
        $this->conditions = $conditions;
    }

    public function AddCondition(AnalysisCondition $condition): AnalysisBlock
    {
        $condition->setBlock($this);
        $this->conditions->add($condition);
        return $this;
    }

    public function removeCondition(AnalysisCondition $condition)
    {
        $this->conditions->removeElement($condition);
    }
}