<?php

declare(strict_types=1);

namespace App\Tests\Test;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class New2Test extends KernelTestCase
{
    public function testTheory()
    {
        /**
         * проверяем гипотезу: типы - это доли.
         */

        // результат недовольного клиента. у него выходило, что они дизайнер, и ему это не нравилось. оно и понятно.
        $userTypes = [
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

//        // мои результаты
//        $userTypes = [
//            'it' => 81,
//            'art' => 56,
//            'boss' => 45,
//            'com' => 36,
//            'tech' => 33,
//            'body' => 33,
//            'hoz' => 22,
//            'human' => 22,
//            'craft' => 19,
//            'math' => 11,
//            'war' => 11,
//            'natural' => 11,
//        ];

//        // Мама
//        $userTypes = [
//            'com' => 89,
//            'boss' => 78,
//            'art' => 67,
//            'human' => 65,
//            'hoz' => 44,
//            'natural' => 41,
//            'craft' => 22,
//            'war' => 22,
//            'tech' => 19,
//            'it' => 19,
//            'body' => 0,
//            'math' => 0,
//        ];

//        // Лёша
//        $userTypes = [
//            'boss' => 89.0,
//            'com' => 69.0,
//            'it' => 67.0,
//            'math' => 61.0,
//            'art' => 56.0,
//            'hoz' => 44.0,
//            'craft' => 39.0,
//            'body' => 33.0,
//            'war' => 33.0,
//            'human' => 29.0,
//            'natural' => 18.0,
//            'tech' => 11.0,
//        ];

//        // Катя
//        $userTypes = [
//            'hoz' => 89,
//            'craft' => 67,
//            'com' => 64,
//            'art' => 44,
//            'boss' => 44,
//            'human' => 28,
//            'it' => 28,
//            'tech' => 17,
//            'body' => 17,
//            'natural' => 12,
//            'war' => 11,
//            'math' => 0,
//        ];

        $professions = [
            ['name' => 'Математик', 'types' => ['math' => 0]], // самый высокий
            ['name' => 'Архитектор', 'types' => ['math' => 60, 'art' => 30, 'com' => 10]],
            ['name' => 'Актёр', 'types' => ['art' => 25, 'body' => 25, 'human' => 25, 'com' => 25]],
            ['name' => 'Режиссёр', 'types' => ['art' => 34, 'human' => 33, 'com' => 33]],
            ['name' => 'Продюсер', 'types' => ['art' => 10, 'hoz' => 20, 'com' => 40, 'boss' => 30]],
            ['name' => 'Кинооператор', 'types' => ['art' => 50, 'com' => 20, 'craft' => 5, 'body' => 25]],
            ['name' => 'Звукорежиссер', 'types' => ['art' => 80, 'com' => 10, 'body' => 10]],
            ['name' => 'Визажист, гримёр', 'types' => ['art' => 45, 'com' => 10, 'craft' => 35]],
            ['name' => 'Мастер маникюра', 'types' => ['art' => 10, 'com' => 45, 'craft' => 45]],
            ['name' => 'Парикмахер', 'types' => ['art' => 30, 'com' => 30, 'craft' => 40]],
            ['name' => 'Дизайнер', 'types' => ['art' => 90, 'com' => 10]],
            ['name' => 'Дизайнер интерьера', 'types' => ['art' => 40, 'com' => 30, 'math' => 30]],
            ['name' => 'Ландшафтный дизайнер', 'types' => ['art' => 50, 'com' => 10, 'math' => 10, 'natural' => 30]],
            ['name' => 'Промышленный дизайнер', 'types' => ['art' => 70, 'tech' => 30]],
            ['name' => 'Композитор', 'types' => ['art' => 100]],
            ['name' => 'Музыкант-исполнитель', 'types' => ['art' => 90, 'com' => 10]],
            ['name' => 'Скульптор', 'types' => ['art' => 70, 'com' => 10, 'craft' => 20]],
            ['name' => 'Художник-иллюстратор', 'types' => ['art' => 90, 'com' => 10]],
            ['name' => 'Искусствовед', 'types' => ['art' => 30, 'human' => 30, 'com' => 40]],
            ['name' => 'Арт-дилер, антиквар', 'types' => ['com' => 35, 'boss' => 55, 'art' => 10]],
            ['name' => 'Ювелир', 'types' => ['art' => 50, 'craft' => 50]],

            ['name' => 'Бизнес-аналитик', 'types' => ['math' => 50, 'com' => 50]],
            ['name' => 'Логист', 'types' => ['math' => 50, 'com' => 50]],
            ['name' => 'Программист', 'types' => ['it' => 65, 'com' => 30, 'math' => 5]],
            ['name' => 'Тестировщик программного обеспечения', 'types' => ['it' => 50, 'com' => 45, 'math' => 5]],

            ['name' => 'Менеджер IT-проекта', 'types' => ['it' => 30, 'com' => 70]],

            ['name' => 'Web-дизайнер', 'types' => ['art' => 70, 'it' => 10, 'com' => 20]],

            ['name' => 'Предприниматель', 'types' => ['boss' => 50, 'com' => 50]],

            ['name' => 'Байер', 'types' => ['com' => 80, 'math' => 20, 'hoz' => 10]],

            ['name' => 'Менеджер гостиницы (отельер)', 'types' => ['com' => 50, 'hoz' => 50]],
            ['name' => 'Мажордом-дворецкий', 'types' => ['com' => 40, 'hoz' => 60]],
            ['name' => 'Хаускипинг-менеджер', 'types' => ['com' => 35, 'hoz' => 65]],

            // учитель
            ['name' => 'Учитель', 'types' => ['human' => 30, 'com' => 70]],
            ['name' => 'Логопед, дефектолог', 'types' => ['natural' => 25, 'human' => 15, 'com' => 60]],
            ['name' => 'Психолог', 'types' => ['natural' => 20, 'human' => 40, 'com' => 40]],

            ['name' => 'Журналист', 'types' => ['com' => 50, 'human' => 50]],

            ['name' => 'Юрист', 'types' => ['com' => 50, 'human' => 50]],


//            ['name' => 'Журналист', 'types' => ['com' => 100]],
//            ['name' => 'Историк', 'types' => ['human' => 100]],
//            ['name' => 'Продюсер', 'types' => ['com' => 60, 'boss' => 40]],
//            ['name' => 'Художник', 'types' => ['art' => 100]],
//            // недоборы
//            ['name' => 'Космонавт', 'types' => ['math' => 80, 'body' => 20]], // высокий и нулевой
            ['name' => 'Товаровед', 'types' => ['hoz' => 30, 'math' => 70]],

            ['name' => 'Реставратор', 'types' => ['hoz' => 30, 'craft' => 70]],
            ['name' => 'Кузнец', 'types' => ['hoz' => 20, 'craft' => 40, 'body' => 40]], // низкий и низкий
            ['name' => 'Солдат', 'types' => ['war' => 50, 'body' => 50]], // низкий и нулевой
            ['name' => 'Каскадёр', 'types' => ['body' => 100]], // нулевой
        ];

        self::calc($userTypes, $professions);
        self::sort($professions);
        self::print($professions);
    }

    private function calc(array $userTypes, array &$professions)
    {
        foreach ($professions as $i => $profession) {
            $professions[$i]['score'] = $this->scoreProfession($userTypes, $profession);
            unset($professions[$i]['types']); // just for pretty printing
        }
    }

    private function scoreProfession(array $userTypes, array $profession): Score
    {
        // отфильтруем типы пользователя, чтобы в них остались только типы, которые есть в профессии
        $userTypes = array_filter($userTypes, function ($name) use ($profession) {
            return array_key_exists($name, $profession['types']);
        }, ARRAY_FILTER_USE_KEY);

        // посчитаем доли
        $parts = [];
        $sum = array_sum($userTypes); // сумма значений для полсчёта доли.
        if ($sum === 0) {
            $sum = 1;
        }
        foreach ($userTypes as $name => $value) {
            $parts[$name] = round(($value * 100) / $sum, 2);
        }

        // считаем балл
        $d = [];
        $score = 0;
        foreach ($parts as $name => $value) {
            $profTypeValue = $profession['types'][$name];

//            $score += $userTypes[$name];
//            $diff = $profTypeValue - $value;
//            if ($diff < 0) {
//                $score += $diff;
//            }
//            $k1 = '';

            // во сколько раз требуемая доля больше набранной (от 0 до небольшого числа)
            // надо 100, набрали 50 - значит 2.
            $k1 = $value == 0 ? 0 : ($profTypeValue / $value);
            // объективно - высокий процент умножаем на пока не постигнутый головой k1
            // ... подумать, как сделать, чтобы значения меньше 1 увеличивало прогрессию. или не надо?
            $k2 = $userTypes[$name] * $k1;
            $score += $k2;

            $d[$name] = round($value) . '%/' . $profTypeValue . '%=' . round($k1, 2);
//            $d[$name] = round($k1, 2);
        }

        $totalScore = round($score / count($parts), 5);

        return new Score($totalScore, $d);
    }

    private function sort(array &$professions)
    {
        usort($professions, function (array $a, array $b) {
            return $b['score']->value() <=> $a['score']->value();
        });
    }

    private static function print(array $professions)
    {
        echo PHP_EOL . '. . . . . . . . . . . . .' . PHP_EOL;
        foreach ($professions as $i => $profession) {
            echo $i . ') ';
            echo $profession['score']->value() . '% - ';
            echo $profession['name'] . ' (';
            foreach ($profession['score']->parts() as $typeName => $typeValue) {
                echo $typeName . ': ' . $typeValue . ', ';
            }
            echo ')' . PHP_EOL;
        }
        echo '. . . . . . . . . . . . .' . PHP_EOL;
    }
}

class Score
{
    private float $value;

    private array $parts;

    public function __construct(float $value, array $parts)
    {
        $this->value = $value;
        $this->parts = $parts;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function parts(): array
    {
        return $this->parts;
    }
}