<?php

namespace App\Test;

use App\Entity\Question;
use App\Entity\QuestionItem;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author: adavydov
 * @since: 25.10.2020
 */
class QuestionXmlMapper
{
    public static function map(DOMElement $node): Question
    {
        $crawler = new Crawler($node);
        $question = new Question();

        // item attributes
        $question->setId($crawler->attr('id'));

        if (($type = $crawler->attr('method')) != null) {
            $question->setType(mb_strtolower($crawler->attr('method')));
        }

        if (($variety = $crawler->attr('group')) != null) {
            $question->setVariety($variety);
        }

        $question->setCount($crawler->attr('count'));

        if (($timer = $crawler->attr('timer')) != null) {
            $question->setTimer($timer);
        }

        if (($showAnswer = $crawler->attr('showAnswer')) != null) {
            $question->setShowAnswer($showAnswer === "true");
        }

        if (($enabledBack = $crawler->attr('enabledBack')) != null) {
            $question->setEnabledBack($enabledBack === "true");
        }

        if (($enabledForward = $crawler->attr('enabledForward')) != null) {
            $question->setEnabledForward($enabledForward === "true");
        }

        // item children
        $nameNode = $crawler->filterXPath('descendant-or-self::name');
        if ($nameNode->count() == 0) {
            throw new \DomainException('Question has to have node "name".');
        }
        $question->setName($nameNode->text());

        if (($text = $crawler->filterXPath('descendant-or-self::text'))->count() > 0) {
            $question->setText($text->text());
        }

        if (($img = $crawler->filterXPath('descendant-or-self::img'))->count() > 0) {
            $question->setImg($img->text());
        }

        if (($right = $crawler->filterXPath('descendant-or-self::right'))->count() > 0) {
            $question->setCorrect($right->text());
        }

        if (($wrong = $crawler->filterXPath('descendant-or-self::wrong'))->count() > 0) {
            $question->setWrong($wrong->text());
        }

        if (($options = $crawler->filterXPath('descendant-or-self::options'))->count() > 0) {
            /**@var DOMElement $option */
            foreach ($options->children() as $option) {
                $question->addItem(
                    QuestionItem::createMinimal(
                        $option->getAttribute('value'),
                        $option->textContent,
                        $option->getAttribute('img'),
                        $option->getAttribute('correct') === "true"));
            }
        }
        if (($fields = $crawler->filterXPath('descendant-or-self::fields'))->count() > 0) {
            /**@var DOMElement $field */
            foreach ($fields->children() as $field) {
                $question->addItem(
                    QuestionItem::createMinimal(
                        $field->getAttribute('correct'),
                        $field->getAttribute('placeholder')));
            }
        }
        return $question;
    }
}