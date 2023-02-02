<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
use App\Test\Helper\ProfessionsMapper;
use App\Test\Helper\ProfessionValueSystemRelevanceCalculator;
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

    const VALUES = ['salary', 'big-company', 'prestige', 'travel', 'promotion', 'self-employ', 'people', 'work-alone', 'gov', 'benefit', 'art', 'indoor', 'outdoor', 'difference', 'publicity', 'safe', 'result', 'intel', 'hands', 'free-time', 'high-society', 'light-work'];

    const GUY = [
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->testTheory();
        return self::SUCCESS;
    }

    private function testTheory()
    {
        $userValues = ['travel', 'hands', 'people', 'result', 'benefit', 'indoor', 'body', 'publicity', 'big-company', 'safe', 'light-work', 'free-time', 'intel', 'high-society', 'work-alone', 'art', 'difference', 'gov', 'promotion', 'outdoor', 'self-employ', 'salary', 'prestige'];
        $userTypes = self::GUY;

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
        $valueCalculator = new ProfessionValueSystemRelevanceCalculator(self::VALUES, $userValues);
        $typesCalculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);

        // todo совместный калькулятор
        foreach ($professions as &$profession) {
            $valueScore = $valueCalculator->calculatePercent($profession->valueSystem());
            $typesScore = $typesCalculator->calculate($profession->types(), $profession->typesNot());

            $profession->setRating($valueScore);
        }
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
            echo ++$index . ') ' . ($profession->getRating()) . ' - ' . $profession->name() . PHP_EOL;
            if ($index === 15) {
                echo '---' . PHP_EOL;
            }
        }

        echo PHP_EOL . '===' . PHP_EOL;
    }

    private function professions(): array
    {
        $xml = $this->kernel->getProjectDir() . '/xml/proftest/professions.xml';
        return (new ProfessionsMapper(file_get_contents($xml), 'ru'))->getProfessions();
    }
}