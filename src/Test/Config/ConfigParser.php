<?php

declare(strict_types=1);

namespace App\Test\Config;

use App\Subscriber\Locale;
use App\Test\Config\Exception\ConfigXmlParsingException;
use App\Test\Config\Struct\Condition\Operator;
use App\Test\Config\Struct\Condition\Variable;
use App\Test\Config\Struct\Scale\Scale;
use App\Test\Config\Struct\Scenario;
use App\Tests\Test\Config\ConfigParserTest;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Структура может иметь неограниченную вложенность.
 * Последняя нода в дереве обязана быть языковой.
 * Содержимое языкового нода интерпретируется как текст.
 *
 * - root
 *      - human
 *          - ru
 *              - <p>Paragraph 1</p>
 *              - <p>Paragraph 2</p>
 *      - settings
 *          - min
 *              - ru
 *                  - 15 рублей
 *
 * @see ConfigParserTest
 */
final readonly class ConfigParser
{
    public function __construct(private Locale $locale)
    {
    }

    public function parse(Crawler $crawler): Config
    {
        $scenarios = $this->parseScenarios($crawler);
        $texts = $this->parseTexts($crawler);

        return new Config($scenarios, $texts);
    }

    private function parseTexts(Crawler $crawler): array
    {
        $rootNode = $crawler->children('texts');

        if ($rootNode->count() == 0) {
            return [];
        }

        // Рекурсивная функция для обработки узлов
        $processNode = function ($node) use (&$processNode) {
            $data = [];

            // Обработка атрибутов узла
            foreach ($node->attributes as $attribute) {
                $data['@attributes'][$attribute->name] = $attribute->value;
            }

            // Обработка дочерних узлов
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType === XML_ELEMENT_NODE) {
                    $name = $childNode->nodeName;
                    if ($name === $this->locale->getValue()) {
                        $data = trim((new Crawler($childNode))->html());
                        break; // needed language found, no reason for continuing loop
                    } else {
                        if ((new Crawler($childNode))->children()->count() === 0) {
                            throw new ConfigXmlParsingException('Every last node must be language like ru or en.');
                        }
                        $childData = $processNode($childNode);
                        if (isset($data[$name])) {
                            if (!is_array($data[$name]) || !isset($data[$name][0])) {
                                $data[$name] = [$data[$name]];
                            }
                            $data[$name][] = $childData;
                        } else {
                            $data[$name] = $childData;
                        }
                    }
                }
            }

            return $data;
        };

        // Преобразование корневого элемента в многомерный массив
        return $processNode($rootNode->getNode(0));
    }

    private function parseScenarios(Crawler $crawler): array
    {
        // --------------------
        // scenarios
        //    scenario
        //       conditions
        //          condition
        //       texts
        //          ru
        //          en
        // --------------------

        $scenariosNode = $crawler->filter('scenarios');
        if ($scenariosNode->count() == 0) {
            return [];
        }

        $scenarios = [];

        foreach ($scenariosNode->children() as $scenarioNode) {
            if ($scenarioNode->nodeName !== 'scenario') {
                throw new ConfigXmlParsingException("Unexpected node \"$scenarioNode->nodeName\", scenario expected.");
            }
            $scenarioNode = new Crawler($scenarioNode);

            // conditions
            $conditionNodes = $scenarioNode->filter('conditions > condition');
            if ($conditionNodes->count() === 0) {
                throw new ConfigXmlParsingException("Scenario does not contain \"condition\" nodes.");
            }
            $conditions = [];
            $conditionNodes->each(function (Crawler $condition) use (&$conditions) {
                $var = new Variable($condition->attr('var'));
                $operator = Operator::fromValue($condition->attr('operator'));
                $value = $condition->attr('value');
                $conditions[] = new Struct\Condition\Condition($var, $operator, $value);
            });

            // text
            $textNode = $scenarioNode->filter('text > ' . $this->locale->getValue());
            if ($textNode->count() === 0) {
                throw new ConfigXmlParsingException("Scenario does not contain \"text\" node.");
            }
            $text = $textNode->html();

            $scale = $this->parseScale($crawler);

            $scenarios[] = new Scenario($conditions, $text, $scale);
        }

        if (count($scenarios) === 0) {
            throw new ConfigXmlParsingException("No scenarios at scenario node.");
        }

        return $scenarios;
    }

    private function parseScale(Crawler $crawler): ?Scale
    {
        // --------------------
        // scale :label :var :max
        // --------------------

        $scaleNode = $crawler->filter('scale');
        if ($scaleNode->count() == 0) {
            return null;
        }

        $label = $scaleNode->attr('label');
        $percentVar = $scaleNode->attr('percentVar');
        $showVar = $scaleNode->attr('showVar');
        $maxVal = (int)$scaleNode->attr('maxVal');

        return new Scale($percentVar, $showVar, $maxVal, $label);
    }
}