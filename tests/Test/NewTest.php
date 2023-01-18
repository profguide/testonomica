<?php

declare(strict_types=1);

namespace App\Tests\Test;

use App\Test\Helper\ProfessionsMapper;
use App\Test\Helper\ProfessionSystemValueRelevanceCalculator;
use App\Test\Proforientation\Profession;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class NewTest extends KernelTestCase
{
    const VALUES = ['salary', 'big-company', 'prestige', 'travel', 'promotion', 'self-employ', 'people', 'work-alone', 'gov', 'benefit', 'art', 'indoor', 'outdoor', 'difference', 'publicity', 'safe', 'result', 'intel', 'hands', 'free-time', 'high-society', 'light-work'];

    public function setUp(): void
    {
        self::bootKernel();
    }

    private function professions(): array
    {
        $xml = self::$kernel->getProjectDir() . '/xml/proftest/professions.xml';
        return (new ProfessionsMapper(file_get_contents($xml), 'ru'))->getProfessions();

//        return [
//            ['name' => 'Архитектор', 'values' => ['art', 'intel', 'benefit', 'result', 'promotion', 'work-alone', 'prestige']],
//            ['name' => 'Актёр', 'values' => ['art', 'publicity', 'difference', 'people', 'hands']],
//            ['name' => 'Режиссёр', 'values' => ['art', 'intel', 'difference', 'people', 'promotion', 'result']],
//            ['name' => 'Кинооператор', 'values' => ['hands', 'art', 'result', 'people', 'travel']],
//            ['name' => 'Звукорежиссер', 'values' => ['work-alone', 'result', 'art', 'indoor', 'safe']],
//            ['name' => 'Визажист', 'values' => ['hands', 'art', 'people', 'result', 'indoor', 'light-work', 'self-employ']],
//            ['name' => 'Мастер маникюра', 'values' => ['hands', 'people', 'result', 'indoor', 'self-employ']],
//            ['name' => 'Дизайнер', 'values' => ['art', 'result', 'intel', 'promotion', 'self-employ', 'light-work', 'work-alone']],
//        ];
    }

    public function test1()
    {
        $professions = self::professions();
//        $userValues = ['salary', 'big-company', 'prestige', 'travel', 'promotion', 'self-employ', 'people', 'work-alone', 'gov', 'benefit', 'art', 'indoor', 'outdoor', 'difference', 'publicity', 'safe', 'result', 'intel', 'hands', 'free-time', 'high-society', 'light-work'];
//        $userValues = ['people'];
//        $userValues = ['art', 'people', 'intel', 'difference', 'promotion', 'result', 'travel', 'safe', 'promotion', 'free-time'];
        $userValues = ['intel', 'indoor', 'people', 'self-employ', 'gov', 'promotion', 'benefit', 'sigh-society', 'light-work', 'safe'];

        self::calculate($professions, $userValues);
        self::sortByScore($professions);

        self::print($professions);

//        die();
    }

    /***
     * @param Profession[] $professions
     * @param array $valuesComb
     */
    private static function calculate(array &$professions, array $valuesComb): void
    {
        $calculator = new ProfessionSystemValueRelevanceCalculator(self::VALUES, $valuesComb);
        foreach ($professions as &$profession) {
            $profession->setValueScore($calculator->calculate($profession->valueSystem()));
        }
    }

    /**
     * @param Profession[] $professions
     */
    private static function sortByScore(array &$professions): void
    {
        usort($professions, function (Profession $a, Profession $b) {
            return $b->getValueScore() - $a->getValueScore();
        });
    }

    /**
     * @param Profession[] $professions
     */
    private static function print(array $professions)
    {
        echo '=== Таблица результатов ===' . PHP_EOL . PHP_EOL;

        foreach ($professions as $index => $profession) {
            echo ($profession->getValueScore()) . ' - ' . $profession->getName() . PHP_EOL;
        }

        echo PHP_EOL . '===' . PHP_EOL;
    }
}