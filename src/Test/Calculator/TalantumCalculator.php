<?php

declare(strict_types=1);

namespace App\Test\Calculator;

use App\Test\AbstractCalculator;
use Symfony\Component\DomCrawler\Crawler;

class TalantumCalculator extends AbstractCalculator
{
    const NON = 0;
    const MIN = 1;
    const MAX = 2;

    function calculate(): array
    {
        $skills = $this->getSkills();

        // инициализируем пустую мапу: сумма баллов скила и максимально возможное значение
        $sums = array_map(function () {
            return [
                'value' => 0,
                'max' => 0,
            ];
        }, $skills);

        // высчитываем набранную сумму баллов каждого скила и их максимально возможное значения
        foreach ($this->answersHolder->getAll() as $answer) {
            $val = $answer->getValue()[0];

            // id сравниваемых скилов.
            $question = $this->questionsHolder->get($answer->getQuestionId());
            $variety = $question->getVariety();
            $skillIds = explode("-", $variety);

            // Определяем какой скил был выбран и его силу.
            // 1 2 3 4 5 - варианты ответов, где
            // 1 и 2 указывают на первый скил (1 - очень, 2 - не очень)
            // 4 и 5 указывают на второй скил (4 - не очень, 5 - очень)
            // 3 - оба скила (или ни один, смотря как интерпретировать)
            switch ($val) {
                case 1:
                    $sums[$skillIds[0]]['value'] += self::MAX;
                    break;
                case 2:
                    $sums[$skillIds[0]]['value'] += self::MIN;
                    break;
                case 3:
                    $sums[$skillIds[0]]['value'] += self::NON;
                    $sums[$skillIds[1]]['value'] += self::NON;
                    break;
                case 4:
                    $sums[$skillIds[1]]['value'] += self::MIN;
                    break;
                case 5:
                    $sums[$skillIds[1]]['value'] += self::MAX;
                    break;
            }

            // Прибавляем максимум к максимальному значению скилов
            $sums[$skillIds[0]]['max'] += self::MAX;
            $sums[$skillIds[1]]['max'] += self::MAX;
        }

        // высчитываем проценты и подготавливаем к выводу
        $result = [];
        foreach ($sums as $id => $conf) {
            $value = $conf['value'];
            $max = $conf['max'];
            if ($max == 0) {
                // для тестирования, а вообще этот сценарий возможен если вдруг
                // переименовали скилы или добавили новые после сохранения результата.
                // так что пусть будет.
                $max = 1;
            }

            $result[$id] = [
                'name' => $skills[$id]['name'],
                'value' => $conf['value'], // debug
                'max' => $max, // debug
                'percentage' => round($value * 100 / $max)
            ];
        }

        // сортируем результ и таким образом формируем топ
        $this->sortResult($result);

        // добавляем текст заключения для скилов, оказавшихся в топ-10
        $this->addTopText($result, 10, $skills);

        return [
            'skills' => $result
        ];
    }

    private function sortResult(array &$result)
    {
        uasort($result, function (array $a, array $b) {
            if ($a['percentage'] == $b['percentage']) {
                return 0;
            }
            return $a['percentage'] < $b['percentage'] ? 1 : -1;
        });
    }

    /**
     * @param Crawler[] $skills
     * @param array $result
     * @param int $max
     */
    private function addTopText(array &$result, int $max, array $skills)
    {
        $i = 1;
        foreach ($result as $k => $row) {
            $result[$k]['text'] = $skills[$k]['report'];
            $i++;
            if ($i > $max) {
                break;
            }
        }
    }

    private static Crawler $configXmlCrawler;

    /**
     * @return array, e.g. ['creative' => 'Креативность'...]
     */
    private function getSkills(): array
    {
        $map = [];

        $config = $this->getConfig();
        $skills = $config->children('skills')->children();
        $skills->each(function (Crawler $skill) use (&$map) {
            $map[$skill->nodeName()] = [
                'name' => $skill->children('text')->text(),
                'report' => $skill->children('report')->text()
            ];
        });

        return $map;
    }

    private function getConfig(): Crawler
    {
        if (empty(self::$configXmlCrawler)) {
            $filename = $this->kernel->getProjectDir() . "/xml/talantum/config.xml";
            $fileContent = file_get_contents($filename);
            self::$configXmlCrawler = new Crawler($fileContent);
        }
        return self::$configXmlCrawler;
    }
}