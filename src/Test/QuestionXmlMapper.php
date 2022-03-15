<?php

namespace App\Test;

use App\Entity\Question;
use App\Entity\QuestionItem;
use App\Util\XmlUtil;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author: adavydov
 * @since: 25.10.2020
 */
class QuestionXmlMapper
{
    public static function map(DOMElement $node, string $locale): Question
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
        $question->setName(XmlUtil::langText($nameNode, $locale));

        $textNode = $crawler->filterXPath('descendant-or-self::text');
        if ($textNode->count() > 0) {
            $question->setText(XmlUtil::langText($textNode, $locale));
        }

        if (($img = $crawler->filterXPath('descendant-or-self::img'))->count() > 0) {
            $question->setImg($img->text());
        }

        if (($right = $crawler->filterXPath('descendant-or-self::right'))->count() > 0) {
            $question->setCorrect(XmlUtil::langText($right, $locale));
        }

        if (($wrong = $crawler->filterXPath('descendant-or-self::wrong'))->count() > 0) {
            $question->setWrong(XmlUtil::langText($wrong, $locale));
        }

        if (($options = $crawler->filterXPath('descendant-or-self::options'))->count() > 0) {
            /**@var DOMElement $option */
            foreach ($options->children() as $option) {
                $question->addItem(
                    QuestionItem::createMinimal(
                        $option->getAttribute('value'),
                        XmlUtil::langText(new Crawler($option), $locale),
                        $option->getAttribute('img'),
                        $option->getAttribute('correct') === "true"));
            }
        }
        if (($fields = $crawler->filterXPath('descendant-or-self::fields'))->count() > 0) {
            /**@var DOMElement $field */
            foreach ($fields->children() as $field) {
                $question->addItem(
                    QuestionItem::createMinimal(
                        XmlUtil::langAttribute($field, 'correct', $locale),
                        XmlUtil::langAttribute($field, 'placeholder', $locale)));
            }
        }
        return $question;
    }
}