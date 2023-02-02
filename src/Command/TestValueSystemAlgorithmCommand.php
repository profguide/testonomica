<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
use App\Test\Helper\ProfessionsMapper;
use App\Test\Helper\ProfessionValueSystemRelevanceCalculator;
use App\Test\Proforientation\Profession;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TestValueSystemAlgorithmCommand extends Command
{
    const VALUES = ['salary', 'big-company', 'prestige', 'travel', 'promotion', 'self-employ', 'people', 'work-alone', 'gov', 'benefit', 'art', 'indoor', 'outdoor', 'difference', 'publicity', 'safe', 'result', 'intel', 'hands', 'free-time', 'high-society', 'light-work'];

    protected static $defaultName = 'app:value-algo';

    private Kernel $kernel;

    public function __construct(Kernel $kernel, string $name = null)
    {
        parent::__construct($name);
        $this->kernel = $kernel;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->testTheory();
        return self::SUCCESS;
    }

    private function professions(): array
    {
        $xml = $this->kernel->getProjectDir() . '/xml/proftest/professions.xml';
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

    private function testTheory()
    {
        $userValues = ['big-company', 'safe', 'light-work', 'free-time', 'high-society', 'indoor', 'result', 'art', 'intel', 'benefit', 'difference', 'work-alone', 'people', 'publicity', 'hands', 'body', 'gov', 'travel', 'promotion', 'outdoor', 'self-employ', 'salary', 'prestige'];
//        $userValues = ['salary', 'big-company', 'prestige', 'travel', 'promotion', 'self-employ', 'people', 'work-alone', 'gov', 'benefit', 'art', 'indoor', 'outdoor', 'difference', 'publicity', 'safe', 'result', 'intel', 'hands', 'free-time', 'high-society', 'light-work'];
//        $userValues = ['people'];
//        $userValues = ['art', 'people', 'intel', 'difference', 'promotion', 'result', 'travel', 'safe', 'promotion', 'free-time'];
//        $userValues = ['intel', 'people', 'self-employ', 'promotion', 'outdoor', 'benefit', 'sigh-society', 'light-work', 'safe', 'gov'];
//        $userValues = ['free-time', 'publicity', 'light-work', 'intel', 'difference', 'people', 'self-employ', 'benefit', 'result', 'promotion', 'indoor', 'gov', 'big-company', 'art', 'outdoor', 'work-alone', 'safe', 'hands', 'body', 'travel', 'high-society', 'salary', 'prestige'];
//        $userValues = ['gov', 'safe', 'big-company', 'light-work', 'self-employ', 'work-alone', 'indoor', 'result', 'art', 'intel', 'benefit', 'difference', 'people', 'publicity', 'hands', 'body', 'free-time', 'high-society', 'travel', 'promotion', 'outdoor', 'salary', 'prestige'];

        $professions = self::professions();

        self::calculate($professions, $userValues);
        self::sortByScore($professions);
        self::print($professions);
    }

    /***
     * @param Profession[] $professions
     * @param array $valuesComb
     */
    private static function calculate(array &$professions, array $valuesComb): void
    {
        $calculator = new ProfessionValueSystemRelevanceCalculator(self::VALUES, $valuesComb);
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
        $color = new Color('red', '', ['bold']);

        echo $color->apply('=== Таблица результатов ===') . PHP_EOL . PHP_EOL;

        foreach ($professions as $index => $profession) {
            echo ++$index . ') ' . ($profession->getValueScore()) . ' - ' . $profession->name() . PHP_EOL;
            if ($index === 15) {
                echo '---' . PHP_EOL;
            }
        }

        echo PHP_EOL . '===' . PHP_EOL;
    }
}