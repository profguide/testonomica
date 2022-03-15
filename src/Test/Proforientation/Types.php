<?php
/**
 * @author: adavydov
 * @since: 24.11.2020
 */

namespace App\Test\Proforientation;


use App\Util\XmlUtil;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

class Types
{
    private static $config;

    public function __construct(KernelInterface $kernel, string $locale)
    {
        $config = [
            'types' => []
        ];

        $crawler = $crawler = new Crawler(file_get_contents($kernel->getProjectDir() . "/xml/proftest/config.xml"));
        $types = $crawler->children('types');

        $types->children()->each(function (Crawler $type) use (&$config, $locale) {

            $interestNode = $type->children('interest');
            $skillsNode = $type->children('skills');

            $config['types'][$type->nodeName()] = [
                'name' => XmlUtil::langText($type->children('name'), $locale),
                'interest' => [
                    'min' => XmlUtil::langText($interestNode->children('min'), $locale),
                    'mid' => XmlUtil::langText($interestNode->children('mid'), $locale),
                    'max' => XmlUtil::langText($interestNode->children('max'), $locale),
                ],
                'skills' => [
                    'min' => XmlUtil::langText($skillsNode->children('min'), $locale),
                    'mid' => XmlUtil::langText($skillsNode->children('mid'), $locale),
                    'max' => XmlUtil::langText($skillsNode->children('max'), $locale),
                ]
            ];
        });

        self::$config = $config;
    }

    public static function interestText(string $typeName, float $value)
    {
        $typeNode = self::typeNode($typeName);
        return self::optTextByValue($typeNode['interest'], $value);
    }

    public static function skillsText(string $typeName, float $value)
    {
        $typeNode = self::typeNode($typeName);
        return self::optTextByValue($typeNode['skills'], $value);
    }

    public static function name(string $typeName)
    {
        return self::$config['types'][$typeName]['name'];
    }

    /**
     * Выбирает один из нескольких текстов по набранным баллам
     * @param $texts
     * @param $value
     * @return mixed
     */
    private static function optTextByValue(array $texts, float $value)
    {
        if ($value >= 66) {
            return $texts['max'];
        } elseif ($value >= 33) {
            return $texts['mid'];
        } else {
            return $texts['min'];
        }
    }

    /**
     * Возвращает подмассив для типа
     * @param string $typeName
     * @return array вида ['interests' => ['сильный текст', 'средний текст', 'слабый текст'], 'skills' => ['сильный', 'средний', 'слабый']]
     */
    private static function typeNode(string $typeName)
    {
        if (empty(self::$config['types'][$typeName])) {
            throw new \RuntimeException('Unknown type name');
        }
        return self::$config['types'][$typeName];
    }

    public static function level($interestValue)
    {
        return self::optTextByValue(['max' => 2, 'mid' => 1, 'min' => 0], $interestValue);
    }
}