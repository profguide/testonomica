<?php
/**
 * @author: adavydov
 * @since: 04.11.2020
 */

namespace App\Test;


use Symfony\Component\DomCrawler\Crawler;

class CrawlerUtil
{
    public static function load(string $fileName): Crawler
    {
        $crawler = new Crawler(file_get_contents($fileName));
        return $crawler->children();
    }
}