<?php

declare(strict_types=1);

namespace App\Test\Config;

use App\Subscriber\Locale;
use App\Test\Config\Exception\ConfigXmlParsingException;
use App\Test\Config\Struct\Condition\Operator;
use App\Test\Config\Struct\Condition\Variable;
use App\Test\Config\Struct\Scale\Level;
use App\Test\Config\Struct\Scale\Levels;
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

    // todo cache
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
                    // проверим имя тега, если оно является языковым, например en.
                    if (in_array($name, Locale::LOCALES)) {
                        // проверим имя тега является ли он текущей локалью
                        if ($name === $this->locale->getValue()) {
                            // дальше вглубь не идём, берём весь внутренний текст
                            $data = trim((new Crawler($childNode))->html());
                            break; // needed language found, no reason for continuing loop
                        }
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
//            if ($conditionNodes->count() === 0) {
//                throw new ConfigXmlParsingException("Scenario does not contain \"condition\" nodes.");
//            }
            $conditions = [];
            $conditionNodes->each(function (Crawler $condition) use (&$conditions) {
                $var = new Variable($condition->attr('var'));
                $operator = Operator::fromValue($condition->attr('operator'));
                $value = $condition->attr('value');
                $conditions[] = new Struct\Condition\Condition($var, $operator, $value);
            });

            // name
            $nameNode = $scenarioNode->filter('name > ' . $this->locale->getValue());
            $name = $nameNode->count() > 0 ? $nameNode->html() : null;

            // text
            $textNode = $scenarioNode->filter('text > ' . $this->locale->getValue());
            $text = $textNode->count() > 0 ? $textNode->html() : null;

            $scale = $this->parseScale($scenarioNode);

            $scenarios[] = new Scenario($conditions, $name, $text, $scale);
        }

        if (count($scenarios) === 0) {
            throw new ConfigXmlParsingException("No scenarios at scenario node.");
        }

        return $scenarios;
    }

    private function parseScale(Crawler $scenario): ?Scale
    {
        // --------------------
        // scale :label :var :max
        // --------------------

        $scaleNode = $scenario->filter('scale');
        if ($scaleNode->count() == 0) {
            return null;
        }

        $percentVar = $scaleNode->attr('percentVar');
        $showVar = $scaleNode->attr('showVar');
        $maxVal = (int)$scaleNode->attr('showMaxVal');

        $labelNode = $scaleNode->filter('label ' . $this->locale->getValue());
        if ($labelNode->count() > 0) {
            $label = $labelNode->text();
        }

        $levelNodes = $scaleNode->filter('levels level');
        if ($levelNodes->count() > 0) {
            $levelsArray = [];
            $levelNodes->each(function (Crawler $levelNode) use (&$levelsArray) {
                $upTo = $levelNode->attr('up');
                $color = $levelNode->attr('color');

                $levelsArray[] = new Level((int)$upTo, $color);
            });

            $levels = new Levels($levelsArray);
        }

        return new Scale($percentVar, $showVar, $maxVal, $label ?? null, $levels ?? null);
    }
}