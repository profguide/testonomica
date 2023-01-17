<?php

declare(strict_types=1);

namespace App\Test\Helper;

use App\Test\CrawlerUtil;
use App\Test\Proforientation\Profession;
use App\Tests\Test\Helper\ProfessionMapperTest;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @see ProfessionMapperTest
 */
final class ProfessionsMapper
{
    private string $content;

    private string $locale;

    public function __construct(string $xmlContent, string $locale)
    {
        $this->content = $xmlContent;
        $this->locale = $locale;
    }

    /**
     * @return Profession[]
     */
    public function getProfessions(): array
    {
        $professions = [];
        foreach (CrawlerUtil::create($this->content) as $professionNode) {
            $professions[] = $this->map(new Crawler($professionNode));
        }
        return $professions;
    }

    private function map(Crawler $crawler): Profession
    {
        return new Profession(
            $this->langText($crawler->children('name')),
            $this->parseCombs($crawler),
            $this->parseSystemValues($crawler),
            $this->parseProfessionNot($crawler),
            $this->parseProfessionDescription($crawler));
    }

    // если будет отнимать много времени, можно сделать lazy, то есть в Profession передавать Crawler всей профессии
    // а когда надо парсить его и доставать нужные части. Вот для описания пригодилось бы. А парсить надо через хелпер
    // ProforientationProfessionMapper::mapDescription($this->crawler);
    // и вообще можно kind сделать объектом ProfessionDescriptionKind
    private function parseProfessionDescription(Crawler $crawler): array
    {
        $description = [];
        $nodeDescription = $crawler->filterXPath('descendant-or-self::description');
        if ($nodeDescription->count() > 0) {
            $kindNodes = $nodeDescription->filterXPath('descendant-or-self::kind');
            if ($kindNodes->count() > 0) {
                foreach ($kindNodes as $kindNode) {
                    $kindCrawler = new Crawler($kindNode);
                    /**@var DOMElement $tag */
                    $kind = [];
                    foreach ($kindCrawler->children() as $tag) {
                        $tagCrawler = new Crawler($tag);
                        $tagLangTexts = $tagCrawler->children();
                        if ($tagLangTexts->count() == 0) {
                            $tagText = $tag->textContent;
                        } else {
                            $tagText = $tagCrawler->children($this->locale)->text();
                        }
                        $kind[$tag->nodeName] = $tagText;
                    }
                    $description[] = $kind;
                }
            }
        }
        return $description;
    }

    private function parseCombs(Crawler $crawler): array
    {
        $arr = [];
        $crawler->children('combs > comb')->each(function (Crawler $comb) use (&$arr) {
            $arr[] = explode(",", trim($comb->attr('comb')));
        });
        return $arr;
    }

    private function parseProfessionNot(Crawler $crawler): array
    {
        $arr = [];
        $not = $crawler->attr('not');
        if (!empty($not)) {
            foreach (explode(",", $not) as $word) {
                $arr[] = trim($word);
            }
        }
        return $arr;
    }

    private function parseSystemValues(Crawler $crawler): array
    {
        $value = $crawler->children('values > value');
        if ($value->count() > 0) {
            return explode(",", $value->attr('value'));
        }

        return [];
    }

    private function langText(Crawler $crawler): string
    {
        $children = $crawler->children();
        if ($children->count() == 0) {
            return $children->text();
        } else {
            return $crawler->children($this->locale)->text();
        }
    }
}