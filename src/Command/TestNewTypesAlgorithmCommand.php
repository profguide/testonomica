<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
use App\Test\Helper\ProfessionsMapper;
use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculatorBasedOnParts;
use App\Test\Proforientation\Calc\ProfessionTypeScoreCalculatorBasedOnTopTypes;
use App\Test\Proforientation\Profession;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Проверяем гипотезу: типы - это доли.
 */
final class TestNewTypesAlgorithmCommand extends Command
{
    protected static $defaultName = 'app:types-algo';

    private Kernel $kernel;

    public function __construct(Kernel $kernel, string $name = null)
    {
        parent::__construct($name);
        $this->kernel = $kernel;
    }

    // Результат недовольного клиента. у него выходило, что они дизайнер, и ему это не нравилось. оно и понятно.
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

    // Мои результаты
    const ARTEM = [
        'it' => 81,
        'art' => 56,
        'boss' => 45,
        'com' => 36,
        'tech' => 33,
        'body' => 33,
        'hoz' => 22,
        'human' => 22,
        'craft' => 19,
//        'math' => 30,
        'math' => 11,
        'war' => 11,
        'natural' => 11,
    ];

    // Мама
    const MAMA = [
        'com' => 89,
        'boss' => 78,
        'art' => 67,
        'human' => 65,
        'hoz' => 44,
        'natural' => 41,
        'craft' => 22,
        'war' => 22,
        'tech' => 19,
        'it' => 19,
        'body' => 0,
        'math' => 0,
    ];

    // Мама 2
    const MAMA_2 = [
        'human' => 82.0,
        'boss' => 78.0,
        'art' => 67.0,
        'com' => 64.0,
        'hoz' => 56.0,
        'natural' => 38.0,
        'war' => 22.0,
        'tech' => 19.0,
        'it' => 19.0,
        'body' => 0.0,
        'math' => 0.0,
        'craft' => 0.0,
    ];

    // Лёша
    const ALEX = [
        'boss' => 89.0,
        'com' => 69.0,
        'it' => 67.0,
        'math' => 61.0,
        'art' => 56.0,
        'hoz' => 44.0,
        'craft' => 39.0,
        'body' => 33.0,
        'war' => 33.0,
        'human' => 29.0,
        'natural' => 18.0,
        'tech' => 11.0,
    ];

    // Катя
    const KATE = [
        'hoz' => 89,
        'craft' => 67,
        'com' => 64,
        'art' => 44,
        'boss' => 44,
        'human' => 28,
        'it' => 28,
        'tech' => 17,
        'body' => 17,
        'natural' => 12,
        'war' => 11,
        'math' => 0,
    ];

    // Катя 2
    const KATE_2 = [
        'hoz' => 78.0,
        'art' => 78.0,
        'craft' => 67.0,
        'com' => 56.0,
        'it' => 50.0,
        'tech' => 25.0,
        'human' => 23.0,
        'math' => 22.0,
        'boss' => 22.0,
        'natural' => 12.0,
        'war' => 11.0,
        'body' => 0.0
    ];

    // Катя 2
    const ARTUR = [
        'art' => 86.0,
        'com' => 77.0,
        'boss' => 75.0,
        'craft' => 50.0,
        'human' => 23.0,
        'it' => 15.0,
        'tech' => 10.0,
        'math' => 5.0,
        'natural' => 5.0,
        'hoz' => 5.0,
        'war' => 5.0,
        'body' => 0.0
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
        $userTypes = self::GUY;
        $professionsToHighlight = ['Маркетолог', 'Психолог', 'Предприниматель', 'Парикмахер'];
        $limit = 0;

        $professions = $this->professions();
        self::calculate($userTypes, $professions);
        self::sort($professions);
        self::print($userTypes, $professions, $limit, $professionsToHighlight);
    }

    /***
     * @param array $userTypes
     * @param Profession[] $professions
     */
    private function calculate(array $userTypes, array $professions)
    {
//        $calculator = new ProfessionTypeScoreCalculatorBasedOnParts($userTypes);
        $calculator = new ProfessionTypeScoreCalculatorBasedOnTopTypes($userTypes);
        foreach ($professions as $i => $profession) {
            $score = $calculator->calculate($profession->types(), $profession->typesNot());
            $professions[$i]->setRating($score->value());
            $professions[$i]->addLog($score->log());
        }
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
        $top = new Color('', '');
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

//    private function localProfessions(): array
//    {
//        $professions = [
//            ['name' => 'Математик', 'types' => ['math' => 0]], // самый высокий
//            ['name' => 'Архитектор', 'types' => ['math' => 60, 'art' => 30, 'com' => 10]],
//            ['name' => 'Актёр', 'types' => ['art' => 25, 'body' => 25, 'human' => 25, 'com' => 25]],
//            ['name' => 'Режиссёр', 'types' => ['art' => 34, 'human' => 33, 'com' => 33]],
//            ['name' => 'Продюсер', 'types' => ['art' => 10, 'hoz' => 20, 'com' => 40, 'boss' => 30]],
//            ['name' => 'Кинооператор', 'types' => ['art' => 50, 'com' => 20, 'craft' => 5, 'body' => 25]],
//            ['name' => 'Звукорежиссер', 'types' => ['art' => 80, 'com' => 10, 'body' => 10]],
//            ['name' => 'Визажист, гримёр', 'types' => ['art' => 45, 'com' => 10, 'craft' => 35]],
//            ['name' => 'Мастер маникюра', 'types' => ['art' => 10, 'com' => 45, 'craft' => 45]],
//            ['name' => 'Парикмахер', 'types' => ['art' => 30, 'com' => 30, 'craft' => 40]],
//            ['name' => 'Дизайнер', 'types' => ['art' => 90, 'com' => 10]],
//            ['name' => 'Дизайнер интерьера', 'types' => ['art' => 40, 'com' => 30, 'math' => 30]],
//            ['name' => 'Ландшафтный дизайнер', 'types' => ['art' => 50, 'com' => 10, 'math' => 10, 'natural' => 30]],
//            ['name' => 'Промышленный дизайнер', 'types' => ['art' => 70, 'tech' => 30]],
//            ['name' => 'Композитор', 'types' => ['art' => 100]],
//            ['name' => 'Музыкант-исполнитель', 'types' => ['art' => 90, 'com' => 10]],
//            ['name' => 'Скульптор', 'types' => ['art' => 70, 'com' => 10, 'craft' => 20]],
//            ['name' => 'Художник-иллюстратор', 'types' => ['art' => 90, 'com' => 10]],
//            ['name' => 'Искусствовед', 'types' => ['art' => 30, 'human' => 30, 'com' => 40]],
//            ['name' => 'Арт-дилер, антиквар', 'types' => ['com' => 35, 'boss' => 55, 'art' => 10]],
//            ['name' => 'Ювелир', 'types' => ['art' => 50, 'craft' => 50]],
//
//            ['name' => 'Бизнес-аналитик', 'types' => ['math' => 50, 'com' => 50]],
//            ['name' => 'Логист', 'types' => ['math' => 50, 'com' => 50]],
//            ['name' => 'Программист', 'types' => ['it' => 65, 'com' => 30, 'math' => 5]],
//            ['name' => 'Тестировщик программного обеспечения', 'types' => ['it' => 50, 'com' => 45, 'math' => 5]],
//
//            ['name' => 'Менеджер IT-проекта', 'types' => ['it' => 30, 'com' => 70]],
//
//            ['name' => 'Web-дизайнер', 'types' => ['art' => 70, 'it' => 10, 'com' => 20]],
//
//            ['name' => 'Предприниматель', 'types' => ['boss' => 50, 'com' => 50]],
//
//            ['name' => 'Байер', 'types' => ['com' => 80, 'math' => 20, 'hoz' => 10]],
//
//            ['name' => 'Менеджер гостиницы (отельер)', 'types' => ['com' => 50, 'hoz' => 50]],
//            ['name' => 'Мажордом-дворецкий', 'types' => ['com' => 40, 'hoz' => 60]],
//            ['name' => 'Хаускипинг-менеджер', 'types' => ['com' => 35, 'hoz' => 65]],
//
//            // учитель
//            ['name' => 'Учитель', 'types' => ['human' => 30, 'com' => 70]],
//            ['name' => 'Логопед, дефектолог', 'types' => ['natural' => 25, 'human' => 15, 'com' => 60]],
//            ['name' => 'Психолог', 'types' => ['natural' => 20, 'human' => 40, 'com' => 40]],
//
//            ['name' => 'Журналист', 'types' => ['com' => 50, 'human' => 50]],
//
//            ['name' => 'Юрист', 'types' => ['com' => 50, 'human' => 50]],
//
//
////            ['name' => 'Журналист', 'types' => ['com' => 100]],
////            ['name' => 'Историк', 'types' => ['human' => 100]],
////            ['name' => 'Продюсер', 'types' => ['com' => 60, 'boss' => 40]],
////            ['name' => 'Художник', 'types' => ['art' => 100]],
////            // недоборы
////            ['name' => 'Космонавт', 'types' => ['math' => 80, 'body' => 20]], // высокий и нулевой
//            ['name' => 'Товаровед', 'types' => ['hoz' => 30, 'math' => 70]],
//
//            ['name' => 'Реставратор', 'types' => ['hoz' => 30, 'craft' => 70]],
//            ['name' => 'Кузнец', 'types' => ['hoz' => 20, 'craft' => 40, 'body' => 40]], // низкий и низкий
//            ['name' => 'Солдат', 'types' => ['war' => 50, 'body' => 50]], // низкий и нулевой
//            ['name' => 'Каскадёр', 'types' => ['body' => 100]], // нулевой
//        ];
//
//        $typedProfessions = [];
//        foreach ($professions as $profession) {
//            $types = [new TypesCombination($profession['types'])];
//
//            $typedProfessions[] = new Profession(
//                $profession['name'],
//                new Types($types),
//                new TypesCombination([]),
//                new ValueSystem([])
//            );
//        }
//
//        return $typedProfessions;
//    }
}