<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Mapper;

use App\Test\Proforientation\ProftestConfig;
use App\Test\Proforientation\Type\Interest;
use App\Test\Proforientation\Type\Skill;
use App\Test\Proforientation\Type\TypesCollection;
use App\Test\Proforientation\TypeConfig;
use App\Util\XmlUtil;
use Symfony\Component\DomCrawler\Crawler;

final class ConfigMapper
{
    private string $locale;

    private string $xmlContent;

    public function __construct(string $xmlContent, string $locale)
    {
        $this->locale = $locale;
        $this->xmlContent = $xmlContent;
    }

    public function parse(): ProftestConfig
    {
        $crawler = new Crawler($this->xmlContent);
        $types = $this->parseTypes($crawler);

        return new ProftestConfig($types);
    }

    private function parseTypes(Crawler $configNode): TypesCollection
    {
        $types = new TypesCollection();

        $typesNodes = $configNode->children('types')->children();
        $typesNodes->each(function (Crawler $type) use ($types) {
            $id = $type->nodeName();
            $name = XmlUtil::langText($type->children('name'), $this->locale);

            $interestNode = $type->children('interest');
            $interest = new Interest(
                XmlUtil::langText($interestNode->children('min'), $this->locale),
                XmlUtil::langText($interestNode->children('mid'), $this->locale),
                XmlUtil::langText($interestNode->children('max'), $this->locale)
            );

            $skillsNode = $type->children('skills');
            $skill = new Skill(
                XmlUtil::langText($skillsNode->children('min'), $this->locale),
                XmlUtil::langText($skillsNode->children('mid'), $this->locale),
                XmlUtil::langText($skillsNode->children('max'), $this->locale)
            );

            $types->add($id, new TypeConfig($name, $interest, $skill));
        });

        return $types;
    }
}