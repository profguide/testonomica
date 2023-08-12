<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ProftestModelCommand extends Command
{
    protected static $defaultName = 'app:proftest-model';

    private Kernel $kernel;

    public function __construct(Kernel $kernel, string $name = null)
    {
        parent::__construct($name);
        $this->kernel = $kernel;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->model();
        return self::SUCCESS;
    }

    private function model()
    {
        $types = [
            'Общение',
            'Творчество',
            'Естественник',
            'Риск',
            'Телесник',
            'Управление',
            'ИТ и математика',
            'Тех',
            'Знаки'
        ];

        $totals = [];
        foreach ($types as $name) {
            $totals[$name] = 0;
        }

        $history = [];

        $i = 0;
        foreach ($types as $name) {
            // сравним себя с другими
            foreach ($types as $_name) {
                // пропускаем себя
                if ($name === $_name) {
                    continue;
                }

                $storyKey = [$name, $_name];
                sort($storyKey);
                $storyKey = implode('', $storyKey);
                if (in_array($storyKey, $history)) {
                    continue;
                }
                $history[] = $storyKey;

                $rating = rand(0, 1);
                if ($rating === 0) {
                    $totals[$name] += 1;
                }
//                elseif ($rating === 1) {
//                    $totals[$name] += 2;
//                }
                elseif ($rating === 1) {
                    $totals[$_name] += 1;
                }
//                elseif ($rating === 3) {
//                    $totals[$_name] += 2;
//                }

                $i++;
            }
        }

        echo 'Итераций: ' . $i . PHP_EOL;

//        // Заполняем рейтинговую таблицу попарными сравнениями
//        for ($i = 0; $i < count($types); $i++) {
//            // Инициализируем сумму для каждого типа
//            for ($j = 0; $j < count($types); $j++) {
//                // Генерируем случайное значение для каждого сравнения
//                $rating = rand(0, 1);
//                $totals[$types[$i]] += $rating;
//            }
//        }

        arsort($totals);
        dd($totals);
//
//
//        // Определяем список блюд
//        $dishes = array(
//            'Общение',
//            'Творчество',
//            'Естественник',
//            'Риск',
//            'Телесник',
//            'Управление',
//            'ИТ и математика',
//            'Тех',
//            'Знаки'
//        );
//
//        // Создаем массив для хранения количества голосов для каждого блюда
//        $votes = array();
//        foreach ($dishes as $dish) {
//            $votes[$dish] = 0;
//        }
//
//        // Определяем результаты голосования
//        for ($i = 0; $i < 36; $i++) {
//            // Генерируем случайные индексы двух блюд
//            $index1 = rand(0, count($dishes) - 1);
//            $index2 = rand(0, count($dishes) - 1);
//            while ($index2 == $index1) {
//                $index2 = rand(0, count($dishes) - 1);
//            }
//            // Увеличиваем количество голосов для одного из двух выбранных блюд
//            $votes[$dishes[$index1]]++;
//        }
//
//        // Сортируем блюда по количеству голосов в порядке убывания
//        arsort($votes);
//
//        dd($votes);
    }
}