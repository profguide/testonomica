<?php

namespace App\Test\Analyzer;

use App\Entity\Analysis;
use App\Entity\Test;
use App\Test\Config\ConfigParser;
use App\Test\Config\ConfigXmlFetcher;
use App\Test\Config\Struct\Condition\Condition;
use App\Test\Config\Struct\Condition\Operator;
use App\Test\Config\Struct\Scenario;

class AnalysisRenderer
{
    public function __construct(
        private readonly ConfigParser     $configParser,
        private readonly ConfigXmlFetcher $configXmlFetcher)
    {
    }

    public function render(Test $test, array $resultData): ?string
    {
        $crawler = $this->configXmlFetcher->fetchByTest($test);
        $config = $this->configParser->parse($crawler);

        if (count($config->scenarios) === 0) {
            return null;
        }

        // todo intro (config->get('intro'))

        $output = $this->playScenarios($config->scenarios, $resultData);
        if (!$output) {
            throw new \RuntimeException("None of the scenarios fit the result.");
        }

        // todo outro (config->get('outro'))

        return $output;

//        $output = "";
//        $blocks = $this->analysisRepository->findBy(['test' => $test]);
//        foreach ($blocks as $block) {
//            $output .= $this->renderAnalysis($block, $resultData);
//        }
//        return $output;
    }

    private function playScenarios(array $scenarios, array $resultData): ?string
    {
        $output = "";
        foreach ($scenarios as $scenario) {
            $output .= $this->playScenario($scenario, $resultData);
        }
        return $output;
    }

    private function playScenario(Scenario $scenario, array $resultData): ?string
    {
        $output = '';

        // todo print scale (if set)

        if ($this->conditionsPass($scenario->conditions, $resultData)) {
            $output .= '<div class="result-block-row__variant">' . $scenario->text . '</div>';
        }

        return $output;
    }

//    private function renderAnalysis(Analysis $analysis, array $resultData): ?string
//    {
//        $output = '';
//        if ($analysis->getTitle()) {
//            $output .= "<h3>{$analysis->getTitle()}</h3>";
//        }
//        $output .= $this->renderScale($analysis, $resultData);
//        $output .= $this->renderVariant($analysis, $resultData);
//        if ($analysis->getText()) {
//            $output .= '<div class="result-block-row__text">' . $analysis->getText() . '</div>';
//        }
//        if (strlen($output) > 0) {
//            return '<div class="result-block-row">' . $output . '</div>';
//        }
//        return null;
//    }
//
//    private function renderScale(Analysis $analysis, array $resultData): ?string
//    {
//        if (!$analysis->getProgressPercentVariableName()) {
//            return null;
//        }
//        $percentage = self::namedVariableValue($resultData, $analysis->getProgressPercentVariableName());
//        if ($percentage == null) {
//            throw new \RuntimeException('Result data does not have ' . $analysis->getProgressPercentVariableName() . '.');
//        }
//        $value = self::namedVariableValue($resultData, $analysis->getProgressVariableName());
//        if ($value) {
//            $maxValue = $analysis->getProgressVariableMax();
//            $text = $value . ' из ' . $maxValue;
//        } else {
//            $text = $percentage . '%';
//        }
//
//        return '<div class="progress">
//                    <div class="progress-bar" role="progressbar" style="width: ' . $percentage . '%;"
//                         aria-valuenow="' . $percentage . '"
//                         aria-valuemin="0"
//                         aria-valuemax="100">' . $text . '
//                    </div>
//                </div>';
//    }
//
//    private function renderVariant(Analysis $analysis, array $resultData): ?string
//    {
//        foreach ($analysis->getBlocks() as $block) {
//            if ($this->conditionsPass($block->getConditions(), $resultData)) {
//                $text = $block->getText();
//                if (str_starts_with($text, '%config.')) {
//                    $text = $this->getTextFromConfig($text, $analysis->getTest());
//                }
//                return '<div class="result-block-row__variant">' . $text . '</div>';
//            }
//        }
//        return null;
//    }

    /**
     * @param Condition[] $conditions
     * @param array $resultData
     * @return bool
     */
    private function conditionsPass(array $conditions, array $resultData): bool
    {
        foreach ($conditions as $condition) {
            if (!$this->conditionPass($condition, $resultData)) {
                return false;
            }
        }

        return true;
    }

    private function conditionPass(Condition $condition, array $resultData): bool
    {
        // нет переменной - не удовлетворяет
        $naturalValue = self::namedVariableValue($resultData, $condition->varName->name);
        if (!$naturalValue) {
            return false;
        }
        return $this->compare(value: (int)$naturalValue, referentValue: $condition->value, comparison: $condition->operator);
    }

    private function compare(int $value, int $referentValue, Operator $comparison): bool
    {
        return match ($comparison) {
            Operator::EQUAL => $value == $referentValue,
            Operator::GREATER => $value > $referentValue,
            Operator::GREATER_OR_EQUAL => $value >= $referentValue,
            Operator::LOWER => $value < $referentValue,
            Operator::LOWER_OR_EQUAL => $value <= $referentValue
        };
    }

    /**
     * Returns nested value of named variable
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
//
//    private function getTextFromConfig(string $text, Test $test): string
//    {
//        if (!$this->config) {
//            $crawler = $this->configXmlFetcher->fetchByTest($test);
//            $this->config = $this->configParser->parse($crawler);
//        }
//
//        return $this->config->get($text);
//    }
}