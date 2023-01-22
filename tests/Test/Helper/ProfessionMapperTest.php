<?php

declare(strict_types=1);

namespace App\Tests\Test\Helper;

use App\Test\Helper\ProfessionsMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProfessionMapperTest extends KernelTestCase
{
    public function testMap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <professions>
                <profession not="war">
                    <name>
                        <ru>Архитектор</ru>
                        <en>Architect</en>
                    </name>
                    <combs>
                        <comb comb="art,tech"/>
                    </combs>
                    <values>
                        <value value="art,intel,benefit,result,promotion,work-alone,prestige"/>
                    </values>
                    <description>
                        <kind>
                            <name>
                                <ru>Архитектор</ru>
                                <en>Architect</en>
                            </name>
                            <pic>https://www.profguide.io/images/article/a/5/KQEVJGQA9r.jpg</pic>
                            <link>https://www.profguide.io/professions/architect.html</link>
                            <text>
                                <ru>Описание на русском</ru>
                                <en>Description in English</en>
                            </text>
                        </kind>
                    </description>
                </profession>
            </professions>';

        $locale = 'ru';

        $mapper = new ProfessionsMapper($xml, $locale);
        $professions = $mapper->getProfessions();


        self::assertCount(1, $professions);

        self::assertEquals('Архитектор', $professions[0]->name());

        self::assertEquals(['art', 'tech'], $professions[0]->types()->combinations()[0]->values());
        self::assertEquals(['war'], $professions[0]->typesNot()->values());

        self::assertEquals(['art', 'intel', 'benefit', 'result', 'promotion', 'work-alone', 'prestige'], $professions[0]->valueSystem()->values());
    }
}