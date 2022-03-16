<?php
/**
 * @author: adavydov
 * @since: 22.12.2020
 */

namespace App\Test;


use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractCalculator implements CalculatorInterface
{
    protected AnswersHolder $answersHolder;

    protected QuestionsHolder $questionsHolder;

    protected KernelInterface $kernel;

    protected string $locale;

    public function __construct(
        AnswersHolder $answersHolder,
        QuestionsHolder $questionsHolder,
        KernelInterface $kernel,
        string $locale = 'ru')
    {
        $this->answersHolder = $answersHolder;
        $this->questionsHolder = $questionsHolder;
        $this->kernel = $kernel;
        $this->locale = $locale;
    }

    /**
     * @var Crawler[]
     */
    private static array $xml = [];

    /**
     * @param string $filename /xml/proftest/config.xml
     * @return Crawler
     */
    protected function xml(string $filename): Crawler
    {
        if (!isset(self::$xml[$filename])) {
            self::$xml[$filename] = new Crawler(file_get_contents($this->kernel->getProjectDir() . $filename));
        }
        return self::$xml[$filename];
    }
}