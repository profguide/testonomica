<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
use App\Test\Helper\ProfessionsMapper;
use App\Test\Proforientation\Calc\ProfessionsPercentCalculator;
use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculatorBasedOnTopTypes;
use App\Test\Proforientation\Profession;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Проверяем гипотезу: типы - это доли.
 */
#[AsCommand(name: 'app:subtypes-algo')]
final class TestSubtypesAlgorithmCommand extends Command
{
    private Kernel $kernel;

    public function __construct(Kernel $kernel, string $name = null)
    {
        parent::__construct($name);
        $this->kernel = $kernel;
    }

    // Результат недовольного клиента. у него выходило, что они дизайнер, и ему это не нравилось. оно и понятно.
    const GUY_MAIN = [
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

    const GUY_SUB = [
        'art' => [
            'muz' => 1,
            'viz' => 0.7
        ]
    ];

    private function professions(): array
    {
        $xml = $this->kernel->getProjectDir() . '/xml/proftest/professions.xml';
        return (new ProfessionsMapper(file_get_contents($xml), 'ru'))->getProfessions();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->testTheory();
        return self::SUCCESS;
    }

    private function testTheory()
    {
        // SETUP
        $userTypes = self::GUY_MAIN;
        $subtypes = self::GUY_SUB;
        $professionsToHighlight = ['Маркетолог', 'Психолог', 'Предприниматель', 'Парикмахер'];
        $limit = 0;

        $professions = $this->professions();
        self::calculate($userTypes, $subtypes, $professions);
        self::sort($professions);
        self::print($userTypes, $professions, $limit, $professionsToHighlight);
    }

    /**
     * @param array $userTypes
     * @param array $subtypes
     * @param Profession[] $professions
     */
    private function calculate(array $userTypes, array $subtypes, array $professions): void
    {
//        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        $calculator = new ProfessionTypeScoreCalculatorBasedOnTopTypes($userTypes, $subtypes);
        foreach ($professions as $profession) {
            $score = $calculator->calculate($profession->types(), $profession->typesNot());
            $profession->setRating($score->value());
            $profession->addLog($score->log());
        }

        (new ProfessionsPercentCalculator())->calculate($professions);
    }

    private function sort(array &$professions)
    {
        usort($professions, function (Profession $a, Profession $b) {
            return $b->getRating() <=> $a->getRating();
        });
    }

    /***
     * @param array $userTypes
     * @param Profession[] $professions
     * @param int $limit
     * @param array $namesToHighlight
     */
    private static function print(array $userTypes, array $professions, int $limit = 0, array $namesToHighlight = [])
    {
        $red = new Color('red', '', ['bold']);
        $gray = new Color('#ccc', '');

        echo self::printUserTypes($userTypes);

        echo PHP_EOL . PHP_EOL;
        echo $red->apply('=========== ПРОФЕССИИ ===========');
        echo PHP_EOL;

        foreach ($professions as $i => $profession) {
            $text = ++$i . ') ' . $profession->getRating() . '% - ' . $profession->name();

            if (in_array($profession->name(), $namesToHighlight)) {
                $text = $red->apply($text);
            } elseif ($profession->getRating() == 0) {
                $text = $gray->apply($text);
            }

            $text .= self::printProfessionTypes($profession);

            echo $text . PHP_EOL;

            if ($limit > 0 && $i >= $limit) {
                break;
            }
        }
        echo '====' . PHP_EOL;
    }

    private static function printProfessionTypes(Profession $profession): string
    {
        $gray = new Color('#ccc', '');

        $text = '';
//        foreach ($profession->types()->combinations() as $c => $combination) {
//            $vars = [];
//            foreach ($combination->values() as $name => $value) {
//                $vars[] = $name . ':' . $value;
//            }
//            $text .= $gray->apply(' (' . implode(', ', $vars) . ')');
//            if ($c < count($profession->types()->combinations()) - 1) {
//                $text .= $gray->apply(',');
//            }
//        }

        $text .= ' ' . $gray->apply(json_encode($profession->getLog()));

        return $text;
    }

    private static function printUserTypes(array $userTypes): string
    {
        $gray = new Color('#ccc', '');

        $vars = [];
        foreach ($userTypes as $name => $value) {
            $vars[] = $gray->apply($name . ':' . $value);
        }

        return PHP_EOL . $gray->apply('=========== ТИПЫ ===========') . PHP_EOL
            . $gray->apply('Types: ')
            . implode(', ', $vars);
    }
}