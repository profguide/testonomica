<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnalysisConditionRepository")
 * @ORM\Table
 * @author: adavydov
 * @since: 15.04.2021
 */
class AnalysisCondition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AnalysisBlock", inversedBy="conditions")
     */
    private AnalysisBlock $block;

    /**
     * @ORM\Column(type="string")
     */
    private string $variableName = "";

    /**
     * @ORM\Column(type="integer")
     */
    private int $referentValue = 0;

    /**
     * @ORM\Column(type="string")
     */
    private string $comparison = "";

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getBlock(): AnalysisBlock
    {
        return $this->block;
    }

    public function setBlock(AnalysisBlock $block): void
    {
        $this->block = $block;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function setVariableName(string $variableName): void
    {
        $this->variableName = $variableName;
    }

    public function getReferentValue(): int
    {
        return $this->referentValue;
    }

    public function setReferentValue(int $referentValue): void
    {
        $this->referentValue = $referentValue;
    }

    public function getComparison(): string
    {
        return $this->comparison;
    }

    public function setComparison(string $comparison): void
    {
        $this->comparison = $comparison;
    }
}