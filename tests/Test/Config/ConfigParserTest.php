<?php

declare(strict_types=1);

namespace App\Tests\Test\Config;

use App\Subscriber\Locale;
use App\Test\Config\ConfigParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RequestStack;

final class ConfigParserTest extends KernelTestCase
{
    public function testCorrectStructure(): void
    {
        $xml = new Crawler('<root><min><ru><p>Вы молодец!</p></ru><en><p>Well done!</p></en></min></root>');
        $parser = new ConfigParser(new Locale(new RequestStack()));
        $config = $parser->parse($xml);

        self::assertNotNull($config);
    }

    public function testNoLanguageThrowsException(): void
    {
        $xml = new Crawler('<root><min><p>Вы молодец!</p></min></root>');
        $parser = new ConfigParser(new Locale(new RequestStack()));

        self::expectExceptionMessage('Every last node must be language like ru or en.');
        $parser->parse($xml);
    }
}