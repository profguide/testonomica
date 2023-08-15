<?php

declare(strict_types=1);

namespace App\Test\Quiz;

use App\Entity\Test;
use Symfony\Component\DomCrawler\Crawler;

final readonly class QuizXmlFetcher
{
    public function __construct(private QuizXmlLocator $xmlLocator)
    {
    }

    public function fetchByTest(Test $test): Crawler
    {
        $filename = $this->xmlLocator->resolveByTest($test);
        return new Crawler(file_get_contents($filename));
    }
}