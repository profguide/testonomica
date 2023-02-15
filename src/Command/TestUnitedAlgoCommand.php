<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
use App\Test\Helper\ProfessionsMapper;
use App\Test\Helper\ProfessionValueSystemRelevanceCalculator;
use App\Test\Proforientation\Calc\ProfessionsPercentCalculator;
use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculatorBasedOnParts;
use App\Test\Proforientation\Profession;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TestUnitedAlgoCommand extends Command
{
    protected static $defaultName = 'app:united-algo';

    private Kernel $kernel;

    public function __construct(Kernel $kernel, string $name = null)
    {
        parent::__construct($name);
        $this->kernel = $kernel;
    }

    const ALL_VALUES = ['salary', 'big-company', 'prestige', 'travel', 'promotion', 'self-employ', 'people', 'work-alone', 'gov', 'benefit', 'art', 'indoor', 'outdoor', 'difference', 'publicity', 'safe', 'result', 'intel', 'hands', 'free-time', 'high-society', 'light-work'];


    const GUY_TYPES = [
        'math' => 100,
        'com' => 92,
        'it' => 67,
        'art' => 67,
        'human' => 60,
        'tech' => 58,
        'boss' => 56,
        'war' => 56,
        'natural' => 41,
        'hoz' => 11,
        'craft' => 8,
        'body' => 0,
    ];

    const GUY_VALUES = [
        'gov',
        'big-company',
        'light-work',
        'safe',
        'indoor',
        'result',
        'art',
        'hands',
        'intel',
        'travel',
        'free-time',
        'difference',
        'body',
        'work-alone',
        'outdoor',
        'promotion',
        'benefit',
        'people',
        'publicity',
        'high-society',
        'self-employ',
        'salary',
        'prestige'
    ];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->testTheory();
        return self::SUCCESS;
    }

    private function testTheory()
    {
        $userValues = self::GUY_VALUES;
        $userTypes = self::GUY_TYPES;

        $professions = $this->professions();

        self::calculate($professions, $userValues, $userTypes);
        self::sortByScore($professions);
        self::print($professions);
    }

    /**
     * @param Profession[] $professions
     * @param array $userValues
     * @param array $userTypes
     */
    private static function calculate(array $professions, array $userValues, array $userTypes)
    {
        $typeMap = [];
        self::calculateTypes($professions, $userTypes);
        foreach ($professions as $profession) {
            $typeMap[$profession->name()] = $profession->getRating();
        }

        $valueMap = [];
        self::calculateValues($professions, $userValues);
        foreach ($professions as $profession) {
            $valueMap[$profession->name()] = $profession->getRating();
        }

        // todo прогоняем массив, соединяем, сохраняем в профессию

        foreach ($professions as $profession) {
            $typePercent = $typeMap[$profession->name()];
            $valuePercent = $valueMap[$profession->name()];

            $totalScore = ($typePercent + $valuePercent) / 2;

            $profession->setRating($totalScore);
        }
    }

    /**
     * @param Profession[] $professions
     * @param array $userTypes
     */
    private static function calculateTypes(array $professions, array $userTypes)
    {
        $typesCalculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        foreach ($professions as $profession) {
            $score = $typesCalculator->calculate($profession->types(), $profession->typesNot());
            $profession->setRating($score->value());
            $profession->addLog(['types' => $score->log()]);
        }

        (new ProfessionsPercentCalculator())->calculate($professions);
    }

    /**
     * @param Profession[] $professions
     * @param array $userValues
     */
    private static function calculateValues(array $professions, array $userValues)
    {
        $valueCalculator = new ProfessionValueSystemRelevanceCalculator(self::ALL_VALUES, $userValues);
        foreach ($professions as $profession) {
            $score = $valueCalculator->calculatePercent($profession->valueSystem());
            $profession->setRating($score->value());
            $profession->addLog(['values' => $score]);
        }

        (new ProfessionsPercentCalculator())->calculate($professions);
    }


    /**
     * @param Profession[] $professions
     */
    private static function sortByScore(array &$professions): void
    {
        usort($professions, function (Profession $a, Profession $b) {
            return $b->getRating() * 100 - $a->getRating() * 100;
        });
    }

    /**
     * @param Profession[] $professions
     */
    private static function print(array $professions)
    {
        $color = new Color('red', '', ['bold']);

        echo $color->apply('=== Таблица результатов ===') . PHP_EOL . PHP_EOL;

        foreach ($professions as $index => $profession) {
            echo ++$index . ') ' . ($profession->getRating()) . ' - ' . $profession->name();
            echo self::profLog($profession);
            echo PHP_EOL;

            if ($index === 15) {
                echo '---' . PHP_EOL;
            }
        }

        echo PHP_EOL . '===' . PHP_EOL;
    }

    private static function profLog(Profession $profession): string
    {
        $gray = new Color('#ccc', '');
        return $gray->apply(json_encode($profession->getLog()));
    }

    private function professions(): array
    {
        $xml = $this->kernel->getProjectDir() . '/xml/proftest/professions.xml';
        return (new ProfessionsMapper(file_get_contents($xml), 'ru'))->getProfessions();
    }
}