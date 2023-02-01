<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Question;
use App\Kernel;
use App\Subscriber\Locale;
use App\Test\QuestionXmlMapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RequestStack;

final class PrintProftestCommand extends Command
{
    protected static $defaultName = 'app:print-proftest';

    private Kernel $kernel;

    public function __construct(Kernel $kernel, string $name = null)
    {
        parent::__construct($name);
        $this->kernel = $kernel;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->print();
        return self::SUCCESS;
    }

    private function print(): void
    {
        echo '==== ВОПРОСЫ ====' . PHP_EOL . PHP_EOL;
        foreach ($this->questions() as $question) {
            echo $question->getVariety() . ') ' . $question->getName() . PHP_EOL;
        }
        echo '====' . PHP_EOL;
    }

    /***
     * @return Question[]
     */
    private function questions(): array
    {
        $questions = [];

        $xml = $this->kernel->getProjectDir() . '/xml/proftest.xml';
        $crawler = new Crawler(file_get_contents($xml));
        foreach ($crawler->children()->children() as $node) {
            $questions[] = QuestionXmlMapper::map($node, new Locale(new RequestStack()));
        }

        return $questions;
    }
}