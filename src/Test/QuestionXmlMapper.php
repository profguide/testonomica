<?php

namespace App\Test;


use Symfony\Component\DomCrawler\Crawler;

/**
 * @author: adavydov
 * @since: 25.10.2020
 */
class QuestionXmlMapper
{
    public static function map(\DOMElement $node): Question
    {
        $crawler = new Crawler($node);
        $question = new Question();

        // item attributes
        $question->setId($crawler->attr('id'));
        $question->setMethod($crawler->attr('method'));
        $question->setGroup($crawler->attr('group'));
        $question->setCount($crawler->attr('count'));
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
        $question->setName($crawler->filterXPath('descendant-or-self::name')->text());
        if (($text = $crawler->filterXPath('descendant-or-self::text'))->count() > 0) {
            $question->setText($text->text());
        }
        if (($img = $crawler->filterXPath('descendant-or-self::img'))->count() > 0) {
            $question->setImg($img->text());
        }
        if (($right = $crawler->filterXPath('descendant-or-self::right'))->count() > 0) {
            $question->setRight($right->text());
        }
        if (($wrong = $crawler->filterXPath('descendant-or-self::wrong'))->count() > 0) {
            $question->setWrong($wrong->text());
        }

        if (($options = $crawler->filterXPath('descendant-or-self::options'))->count() > 0) {
            /**@var \DOMElement $option */
            foreach ($options->children() as $option) {
                $question->addOption(
                    new Option(
                        $option->getAttribute('value'),
                        $option->getAttribute('correct') === "true",
                        $option->textContent));
            }
        }

        if (($fields = $crawler->filterXPath('descendant-or-self::fields'))->count() > 0) {
            /**@var \DOMElement $field */
            foreach ($fields->children() as $field) {
                $question->addField(
                    new Field(
                        $field->getAttribute('type'),
                        $field->getAttribute('placeholder'),
                        $field->getAttribute('correct')));
            }
        }
        return $question;
    }
}