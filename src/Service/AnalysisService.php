<?php

namespace App\Service;

use App\Entity\Analysis;
use App\Entity\AnalysisCondition;
use App\Entity\Test;
use App\Repository\AnalysisRepository;
use Doctrine\Common\Collections\Collection;

class AnalysisService
{
    private AnalysisRepository $analysisRepository;

    public function __construct(AnalysisRepository $blockRepository)
    {
        $this->analysisRepository = $blockRepository;
    }

    public function render(Test $test, array $resultData): string
    {
        $output = "";
        $blocks = $this->analysisRepository->findBy(['test' => $test]);
        foreach ($blocks as $block) {
            $output .= $this->renderAnalysis($block, $resultData);
        }
        return $output;
    }

    private function renderAnalysis(Analysis $analysis, array $resultData): ?string
    {
        $output = '';
        if ($analysis->getTitle()) {
            $output .= "<h3>{$analysis->getTitle()}</h3>";
        }
        $output .= $this->renderScale($analysis, $resultData);
        $output .= $this->renderVariant($analysis, $resultData);
        if (strlen($output) > 0) {
            return '<div class="result-block-row">' . $output . '</div>';
        }
        return null;
    }

    private function renderScale(Analysis $analysis, array $resultData): ?string
    {
        if (!$analysis->getProgressPercentVariableName()) {
            return null;
        }
        if (!isset($resultData[$analysis->getProgressPercentVariableName()])) {
            throw new \RuntimeException('Result data does not have ' . $analysis->getProgressPercentVariableName());
        }
        $percentage = $resultData[$analysis->getProgressPercentVariableName()];
        $value = $resultData[$analysis->getProgressVariableName()] ?? null;
        if ($value) {
            $maxValue = $analysis->getProgressVariableMax();
            $text = $value . ' из ' . $maxValue;
        } else {
            $text = $percentage . '%';
        }

        return '<div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: ' . $percentage . '%;"
                         aria-valuenow="' . $percentage . '" 
                         aria-valuemin="0"
                         aria-valuemax="100">' . $text . '
                    </div>
                </div>';
    }

    private function renderVariant(Analysis $analysis, array $resultData): ?string
    {
        foreach ($analysis->getBlocks() as $block) {
            if ($this->conditionsPass($block->getConditions(), $resultData)) {
                return $block->getText();
            }
        }
        return null;
    }

    private function conditionsPass(Collection $conditions, array $resultData): bool
    {
        foreach ($conditions as $condition) {
            if (!$this->conditionPass($condition, $resultData)) {
                return false;
            }
        }
        return true;
    }

    private function conditionPass(AnalysisCondition $condition, array $resultData): bool
    {
        $variableName = $condition->getVariableName();
        // нет переменной - не удовлетворяет
        if (!isset($resultData[$variableName])) {
            return false;
        }
        $resultValue = $resultData[$variableName];
        $referentValue = $condition->getReferentValue();
        return $this->compare((int)$resultValue, $referentValue, $condition->getComparison());
    }

    private function compare(int $value, int $referentValue, string $comparison): bool
    {
        switch ($comparison) {
            case "==":
                return $value == $referentValue;
            case ">":
                return $value > $referentValue;
            case ">=":
                return $value >= $referentValue;
            case "<":
                return $value < $referentValue;
            case "<=":
                return $value <= $referentValue;
            default:
                throw new \InvalidArgumentException("Unsupported comparison operation $comparison");
        }
    }
}