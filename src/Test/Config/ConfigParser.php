<?php

declare(strict_types=1);

namespace App\Test\Config;

use App\Subscriber\Locale;
use App\Test\Config\Exception\ConfigXmlParsingException;
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
        return new Config($this->xmlToArray($crawler));
    }

    private function xmlToArray(Crawler $crawler): array
    {
        // Получаем корневой элемент Crawler
        $rootNode = $crawler->filter(':root');

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
}