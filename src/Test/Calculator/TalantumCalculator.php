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

        // сумма баллов скила и максимально возможное значение
        $sums = array_map(function () {
            return [
                'value' => 0,
                'max' => 0,
            ];
        }, $skills);

        foreach ($this->answersHolder->getAll() as $answer) {
            $val = $answer->getValue()[0];

            // id сравниваемых скилов.
            $question = $this->questionsHolder->get($answer->getQuestionId());
            $variety = $question->getVariety();
            $skillIds = explode("-", $variety);

            // значения лежат в диапазоне от 1 до 5.
            // 1 и 2 - первый скил
            // 3 - оба скила или ни один скил (смотря как интерпретировать)
            // 4 и 5 - второй скил
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

            // инкремент максимального значения для обоих скилов
            $sums[$skillIds[0]]['max'] += self::MAX;
            $sums[$skillIds[1]]['max'] += self::MAX;
        }

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
                'value' => $conf['value'],
                'percentage' => round($value * 100 / $max)
            ];
        }

        //        arsort($groupsSums);

        return [
            'skills' => $result
        ];
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
                'name' => $skill->children('text')->text()
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