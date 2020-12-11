<?php

namespace App\Test;

use App\Entity\Test;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author: adavydov
 * @since: 23.10.2020
 * Такие планы по кешированию, если окажется, что медленно работает
 * 1 вынести логику работы с XML в отдельный класс SourceXmlRepository
 * 2 добавить SourceCacheRepositoryInterface <- SourceRedisRepository
 * 3 здесь сделать работу с обоими репозиториями, используя такие методы:
 * $this->xmlRepository->load($test)
 * $this->redisRepository->isTestPersist($test)
 * $this->redisRepository->saveAll(Question[])
 */
class SourceRepository implements SourceRepositoryInterface
{
    private $kernel;

    private static $xml = null;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getQuestion(Test $test, $itemId)
    {
        $items = $this->getItems($test);
        $position = $this->getItemPosition($items, $itemId);
        /**@var DOMElement $item */
        if (($item = $items->getNode($position))) {
            return QuestionXmlMapper::map($item);
        }
        return null;
    }

    public function getNextQuestion(Test $test, $itemId): ?Question
    {
        $items = $this->getItems($test);
        $nextPosition = $this->getItemPosition($items, $itemId) + 1;
        /**@var DOMElement $nextItem */
        if (($nextItem = $items->getNode($nextPosition))) {
            return QuestionXmlMapper::map($nextItem);
        }
        return null;
    }

    public function getPrevQuestion(Test $test, $itemId): ?Question
    {
        $items = $this->getItems($test);
        $prevPosition = $this->getItemPosition($items, $itemId) - 1;
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

    public function getQuestionNumber(Test $test, Question $question)
    {
        return $this->getItemPosition($this->getItems($test), $question->getId()) + 1;
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

    public function getTotalCount(Test $test)
    {
        return $this->getItems($test)->count();
    }

    private function getItemPosition(Crawler $items, $itemId)
    {
        /**@var Crawler $items */
        for ($i = 0; $i < count($items); $i++) {
            /**@var DOMElement $node */
            $node = $items->getNode($i);
            if ($node->getAttribute('id') == $itemId) {
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
        if (self::$xml == null) {
            $name = $test->getXmlFilename() ?? $test->getId(); // TestXmlFileNameResolver?
            $filename = $this->kernel->getProjectDir() . "/xml/{$name}.xml";
            $fileContent = file_get_contents($filename);
            self::$xml = new Crawler($fileContent);
        }
        return self::$xml;
    }
}