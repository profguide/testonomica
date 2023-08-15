<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
use App\Test\Helper\ProfessionsMapper;
use App\Test\Helper\ProfessionValueSystemRelevanceCalculator;
use App\Test\Proforientation\Calc\ProfessionsPercentCalculator;
use App\Test\Proforientation\Profession;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:value-algo')]
final class TestValueSystemAlgorithmCommand extends Command
{
    // не менять
    const VALUES = ['salary', 'big-company', 'prestige', 'travel', 'promotion', 'self-employ', 'people', 'work-alone', 'gov', 'benefit', 'art', 'indoor', 'outdoor', 'difference', 'publicity', 'safe', 'result', 'intel', 'hands', 'free-time', 'high-society', 'light-work'];

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
        // проблема видимо в том, что везде разное количество. так? как это может влиять?

//        // Военный: gov,body,people,travel,difference,outdoor,promotion
//        $userValues = [
//            'gov', // <<
//            'body', // <<
//            'people', // <<
//            'travel', // <<
//            'difference', // <<
//            'outdoor', // <<
//            'promotion', // <<
//            'art', // не военнослужащий
//            'publicity', // не военнослужащий
//            'safe', // не военнослужащий
//            'work-alone', // не военнослужащий
//            'hands',// не военнослужащий
//            'intel',// не военнослужащий
//            'benefit',// не военнослужащий
//            'big-company',// не военнослужащий
//            'light-work',// не военнослужащий
//            'free-time',// не военнослужащий
//            'high-society',// не военнослужащий
//            'indoor',// не военнослужащий
//            'result',// не военнослужащий
//            'self-employ', // не военнослужащий
//            'salary', // не военнослужащий
//            'prestige' // не военнослужащий
//        ];

//        // Стилист-имиджмейкер: people,travel,result,body,art,free-time,light-work,difference,indoor
//        $userValues = [
//            'people',
//            'travel',
//            'result',
//            'body',
//            'art',
//            'free-time',
//            'light-work',
//            'difference',
//            'indoor',
//            'gov',
//            'outdoor',
//            'promotion',
//            'publicity',
//            'safe',
//            'work-alone',
//            'hands',
//            'intel',
//            'benefit',
//            'big-company',
//            'high-society',
//            'self-employ',
//            'salary',
//            'prestige'
//        ];

//        // актёр art,publicity,difference,people,hands
//        $userValues = [
//            'art',
//            'publicity',
//            'difference',
//            'people',
//            'body',
//            'safe',
//            'free-time',
//            'travel',
//            'result',
//            'hands',
//            'light-work',
//            'indoor',
//            'gov',
//            'outdoor',
//            'promotion',
//            'work-alone',
//            'intel',
//            'benefit',
//            'big-company',
//            'high-society',
//            'self-employ',
//            'salary',
//            'prestige'
//        ];

        $userValues = [
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
    private static function calculate(array $professions, array $valuesComb): void
    {
        $calculator = new ProfessionValueSystemRelevanceCalculator(self::VALUES, $valuesComb);
        foreach ($professions as $profession) {
            $score = $calculator->calculatePercent($profession->valueSystem());
            $profession->setRating($score->value());
            $profession->addLog($score->log());
        }

        (new ProfessionsPercentCalculator())->calculate($professions);
    }

    /**
     * @param Profession[] $professions
     */
    private static function sortByScore(array &$professions): void
    {
        usort($professions, function (Profession $a, Profession $b) {
            return $b->getRating() - $a->getRating();
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
}