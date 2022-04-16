<?php

declare(strict_types=1);

namespace App\Util;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class XmlUtil
{
    /**
     * Два варианта:
     * <tag>Текст</tag>
     *
     * <tag>
     *  <ru>Текст</ru>
     *  <en>Text</en>
     * </tag>
     *
     * @param Crawler $crawler
     * @param string $locale
     * @return string
     */
    public static function langText(Crawler $crawler, string $locale): string
    {
        if ($crawler->children()->count() == 0) {
            return $crawler->text();
        } else {
            return $crawler->children($locale)->text();
        }
    }

    /**
     * Locale attribute: placeholder-ru, placeholder-en, placeholder
     * If no locale attribute found, base attribute will be used (placeholder).
     *
     * @param DOMElement $field
     * @param string $baseAttrName e.g. placeholder|correct
     * @param string $locale ru|en
     * @return string|null
     */
    public static function langAttribute(DOMElement $field, string $baseAttrName, string $locale): ?string
    {
        $localeAttrName = $baseAttrName . '-' . $locale;
        $localeAttributeValue = $field->getAttribute($localeAttrName);
        if (!empty($localeAttributeValue)) {
            return $localeAttributeValue;
        }
        return $field->getAttribute($baseAttrName);
    }
}