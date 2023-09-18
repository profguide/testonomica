<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Test;
use App\Subscriber\Locale;
use App\Test\QuestionXmlMapper;
use App\Test\Quiz\QuizXmlFetcher;
use App\Test\TestItemNotFoundException;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class XmlSourceRepository implements SourceRepositoryInterface
{
    private const INSTRUCTION_NODE_NAME = 'instruction';
    private const QUESTIONS_NODE_NAME = 'items';

    private Locale $locale;

    // for request cache
    private static array $xml = [];

    public function __construct(private readonly QuizXmlFetcher $quizXmlFetcher, Locale $locale)
    {
        $this->locale = $locale;
    }

    public function getQuestion(Test $test, $id): Question
    {
        $items = $this->getItems($test);
        $position = $this->getItemPosition($items, $id);
        return QuestionXmlMapper::map($items->getNode($position), $this->locale);
    }

    public function getNextQuestion(Test $test, $id): ?Question
    {
        $items = $this->getItems($test);
        $nextPosition = $this->getItemPosition($items, $id) + 1;
        /**@var DOMElement $nextItem */
        if (($nextItem = $items->getNode($nextPosition))) {
            return QuestionXmlMapper::map($nextItem, $this->locale);
        }
        return null;
    }

    public function getPrevQuestion(Test $test, $id): ?Question
    {
        $items = $this->getItems($test);
        $prevPosition = $this->getItemPosition($items, $id) - 1;
        /**@var DOMElement $nextItem */
        if (($nextItem = $items->getNode($prevPosition))) {
            return QuestionXmlMapper::map($nextItem, $this->locale);
        }
        return null;
    }

    public function getFirstQuestion(Test $test): Question
    {
        /**@var DOMElement $firstNode */
        $firstNode = $this->getItems($test)->getNode(0);
        return QuestionXmlMapper::map($firstNode, $this->locale);
    }

    public function getLastQuestion(Test $test): Question
    {
        $nodes = $this->getItems($test);
        /**@var DOMElement $lastNode */
        $lastNode = $nodes->getNode($nodes->count() - 1);
        return QuestionXmlMapper::map($lastNode, $this->locale);
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
            $question = QuestionXmlMapper::map($node, $this->locale);
            $questions[$question->getId()] = $question;
        }
        return $questions;
    }

    public function getTotalCount(Test $test): int
    {
        return $this->getItems($test)->count();
    }

    public function getInstruction(Test $test): ?string
    {
        $instruction = $this->getXml($test)->children(self::INSTRUCTION_NODE_NAME);
        if ($instruction->count() === 0) {
            return null;
        }

        $localeNode = $instruction->children($this->locale->getValue());
        if ($localeNode->count() === 0) {
            return null;
        }

        return trim($localeNode->html());
    }

    private function getItemPosition(Crawler $items, $id): int
    {
        for ($i = 0; $i < count($items); $i++) {
            /**@var DOMElement $node */
            $node = $items->getNode($i);
            if ($node->getAttribute('id') == $id) {
                return $i;
            }
        }

        throw new TestItemNotFoundException();
    }

    private function getItems(Test $test): Crawler
    {
        $items = $this->getXml($test)->children(self::QUESTIONS_NODE_NAME);
        return $items->children();
    }

    private function getXml(Test $test): Crawler
    {
        if (empty(self::$xml[$test->getId()])) {
            self::$xml[$test->getId()] = $this->quizXmlFetcher->fetchByTest($test);
        }
        return self::$xml[$test->getId()];
    }
}