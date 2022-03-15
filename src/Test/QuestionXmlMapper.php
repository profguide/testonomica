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
        $question->setName(self::langText($nameNode, $locale));

        $textNode = $crawler->filterXPath('descendant-or-self::text');
        if ($textNode->count() > 0) {
            $question->setText(self::langText($textNode, $locale));
        }

        if (($img = $crawler->filterXPath('descendant-or-self::img'))->count() > 0) {
            $question->setImg($img->text());
        }

        if (($right = $crawler->filterXPath('descendant-or-self::right'))->count() > 0) {
            $question->setCorrect(self::langText($right, $locale));
        }

        if (($wrong = $crawler->filterXPath('descendant-or-self::wrong'))->count() > 0) {
            $question->setWrong(self::langText($wrong, $locale));
        }

        if (($options = $crawler->filterXPath('descendant-or-self::options'))->count() > 0) {
            /**@var DOMElement $option */
            foreach ($options->children() as $option) {
                $question->addItem(
                    QuestionItem::createMinimal(
                        $option->getAttribute('value'),
                        self::langText(new Crawler($option), $locale),
                        $option->getAttribute('img'),
                        $option->getAttribute('correct') === "true"));
            }
        }
        if (($fields = $crawler->filterXPath('descendant-or-self::fields'))->count() > 0) {
            /**@var DOMElement $field */
            foreach ($fields->children() as $field) {
                $question->addItem(
                    QuestionItem::createMinimal(
                        self::langAttribute($field, 'correct', $locale),
                        self::langAttribute($field, 'placeholder', $locale)));
            }
        }
        return $question;
    }

    private static function langText(Crawler $crawler, string $locale): string
    {
        if ($crawler->children()->count() == 0) {
            return $crawler->text();
        } else {
            return $crawler->children($locale)->text();
        }
    }

    /**
     * Locale attribute: placeholder-ru, placeholder-en, placeholder
     * If no locale attribute found, base attribute will be used (placeholder).
     *
     * @param DOMElement $field
     * @param string $baseAttrName e.g. placeholder|correct
     * @param string $locale ru|en
     * @return string|null
     */
    private static function langAttribute(DOMElement $field, string $baseAttrName, string $locale): ?string
    {
        $localeAttrName = $baseAttrName . '-' . $locale;
        return $field->getAttribute($localeAttrName) ?? $field->getAttribute($baseAttrName);
    }
}