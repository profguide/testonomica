<?php

namespace App\Test\Analyzer;

use App\Entity\Analysis;
use App\Entity\AnalysisCondition;
use App\Entity\Test;
use App\Repository\AnalysisRepository;
use App\Test\Config\Config;
use App\Test\Config\ConfigParser;
use App\Test\Config\ConfigXmlFetcher;
use Doctrine\Common\Collections\Collection;

class AnalysisRenderer
{
    public function __construct(
        private readonly AnalysisRepository $analysisRepository,
        private readonly ConfigParser       $configParser,
        private readonly ConfigXmlFetcher   $configXmlFetcher)
    {
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
        if ($analysis->getText()) {
            $output .= '<div class="result-block-row__text">' . $analysis->getText() . '</div>';
        }
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
        $percentage = self::namedVariableValue($resultData, $analysis->getProgressPercentVariableName());
        if ($percentage == null) {
            throw new \RuntimeException('Result data does not have ' . $analysis->getProgressPercentVariableName() . '.');
        }
        $value = self::namedVariableValue($resultData, $analysis->getProgressVariableName());
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
                $text = $block->getText();
                if (str_starts_with($text, '%config.')) {
                    $text = $this->getTextFromConfig($text, $analysis->getTest());
                }
                return '<div class="result-block-row__variant">' . $text . '</div>';
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
        $resultValue = self::namedVariableValue($resultData, $variableName);
        if (!$resultValue) {
            return false;
        }
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

    /**
     * Returns nested a nested value of named variable
     * E.g. REPEATS.Racionalniy.percentage
     *
     * @param array $resultData
     * @param string $varName
     * @return string|null
     */
    private static function namedVariableValue(array $resultData, string $varName): ?string
    {
        $varLevels = explode('.', $varName);
        $value = $resultData;
        foreach ($varLevels as $varLevel) {
            if (!isset($value[$varLevel])) {
                return null;
            }
            $value = $value[$varLevel];
        }
        return $value;
    }

    private ?Config $config = null;

    private function getTextFromConfig(string $text, Test $test): string
    {
        if (!$this->config) {
            $crawler = $this->configXmlFetcher->fetchByTest($test);
            $this->config = $this->configParser->parse($crawler);
        }

        return $this->config->get($text);
    }
}