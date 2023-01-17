<?php
/**
 * @author: adavydov
 * @since: 04.11.2020
 */

namespace App\Test;


use Symfony\Component\DomCrawler\Crawler;

class CrawlerUtil
{
    public static function create(string $content): Crawler
    {
        $crawler = new Crawler($content);
        return $crawler->children();
    }

    public static function load(string $fileName): Crawler
    {
        return self::create(file_get_contents($fileName));
    }
}