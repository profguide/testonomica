<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Test;
use App\Test\QuestionXmlMapper;
use App\Test\TestItemNotFoundException;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

class XmlSourceRepository implements SourceRepositoryInterface
{
    private KernelInterface $kernel;

    // for request cache
    private static array $xml = [];

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getQuestion(Test $test, $id): Question
    {
        $items = $this->getItems($test);
        $position = $this->getItemPosition($items, $id);
        return QuestionXmlMapper::map($items->getNode($position));
    }

    public function getNextQuestion(Test $test, $id): ?Question
    {
        $items = $this->getItems($test);
        $nextPosition = $this->getItemPosition($items, $id) + 1;
        /**@var DOMElement $nextItem */
        if (($nextItem = $items->getNode($nextPosition))) {
            return QuestionXmlMapper::map($nextItem);
        }
        return null;
    }

    public function getPrevQuestion(Test $test, $id): ?Question
    {
        $items = $this->getItems($test);
        $prevPosition = $this->getItemPosition($items, $id) - 1;
        /**@var DOMElement $nextItem */
        if (($nextItem = $items->getNode($prevPosition))) {
            return QuestionXmlMapper::map($nextItem);
        }
        return null;
    }

    public function getFirstQuestion(Test $test): Question
    {
        /**@var DOMElement $firstNode */
        $firstNode = $this->getItems($test)->getNode(0);
        return QuestionXmlMapper::map($firstNode);
    }

    public function getLastQuestion(Test $test): Question
    {
        $nodes = $this->getItems($test);
        /**@var DOMElement $lastNode */
        $lastNode = $nodes->getNode($nodes->count() - 1);
        return QuestionXmlMapper::map($lastNode);
    }

    public function getQuestionNumber(Test $test, $id): int
    {
        return $this->getItemPosition($this->getItems($test), $id) + 1;
    }

    public function getAllQuestions(Test $test): array
    {
        $questions = [];
        $nodes = $this->getItems($test);
        foreach ($nodes as $node) {
            $question = QuestionXmlMapper::map($node);
            $questions[$question->getId()] = $question;
        }
        return $questions;
    }

    public function getTotalCount(Test $test): int
    {
        return $this->getItems($test)->count();
    }

    private function getItemPosition(Crawler $items, $id): int
    {
        /**@var Crawler $items */
        for ($i = 0; $i < count($items); $i++) {
            /**@var DOMElement $node */
            $node = $items->getNode($i);
            if ($node->getAttribute('id') == $id) {
                return $i;
            }
        }
        throw new TestItemNotFoundException();
    }

    private function getItems(Test $test)
    {
        $items = $this->getXml($test)->children();
        return $items->children();
    }

    private function getXml(Test $test): Crawler
    {
        if (empty(self::$xml[$test->getId()])) {
            $name = $test->getXmlFilename() ?? $test->getId(); // TestXmlFileNameResolver?
            $filename = $this->kernel->getProjectDir() . "/xml/{$name}.xml";
            $fileContent = file_get_contents($filename);
            self::$xml[$test->getId()] = new Crawler($fileContent);
        }
        return self::$xml[$test->getId()];
    }
}