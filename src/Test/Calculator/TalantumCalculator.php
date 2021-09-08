<?php

declare(strict_types=1);

namespace App\Test\Calculator;

use App\Test\AbstractCalculator;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;
use Symfony\Component\DomCrawler\Crawler;

class TalantumCalculator extends AbstractCalculator
{
    private const SKILL_NAMES = [
        'creative' => 'Креативность',
        'flex' => 'Гибкость',
        'time' => 'Обращение со временем',
        'feedback' => 'Использование обратной связи',
        'wide' => 'Широта взглядов',
        'solve' => 'Способность решать проблемы',
        'search' => 'Поисковая активность',
    ];

    private const SKILL_GROUP_NAMES = [
        'creative-1' => 'Я прокладываю новые пути',
        'creative-2' => 'У меня всегда есть идеи',
        'creative-3' => 'Я улучшаю',
        'creative-4' => 'Меня считают творческим',
        'flex-1' => 'Приспособление к людям, учет их различий',
        'flex-2' => 'Быстрая смена деятельности',
        'flex-3' => 'Смена способов работы',
        'flex-4' => 'Смена взглядов и образа жизни',
        'time-1' => 'Планирование',
        'time-2' => 'Все делать по графику',
        'time-3' => 'Чувствовать время: успевать',
        'feedback-1' => 'Я рад критике, так как это помогает расти',
        'feedback-2' => 'Коллективный труд дает обмен мнениями',
        'feedback-3' => 'Я не рад критике, но я ею пользуюсь обязательно',
        'wide-1' => 'Политика, религия и искусства',
        'wide-2' => 'Пол',
        'wide-3' => 'Профессия',
        'wide-4' => 'Быт',
        'solve-1' => 'Меня зовут решать проблему',
        'solve-2' => 'Объективен ли я',
        'solve-3' => 'Я накапливаю проблемы',
        'solve-4' => 'Хладнокровен ли я',
        'search-1' => 'Общая поисковая активность',
        'search-2' => 'Поисковая активность на работе и в учебе',
    ];

    private static $resultXml;

    function calculate(): array
    {
        $skills = [];
        foreach ($this->questionsHolder->byGroupsMask() as $name => $skill) {
            $skillQuestionHolder = new QuestionsHolder($skill);
            $sum = AnswersUtil::sum($skillQuestionHolder, $this->answersHolder);
            $max = AnswersUtil::max($skillQuestionHolder);
            $skills[$name] = [
                'name' => self::SKILL_NAMES[$name],
                'total' => [
                    'sum' => $sum,
                    'max' => $max,
                    'percentage' => round($sum * 100 / $max)
                ],
            ];
        }

        foreach ($this->questionsHolder->byGroups() as $name => $group) {
            $groupQuestionHolder = new QuestionsHolder($group);
            $sum = AnswersUtil::sum($groupQuestionHolder, $this->answersHolder);
            $max = AnswersUtil::max($groupQuestionHolder);
            $percentage = round($sum * 100 / $max);
            $skillName = strstr($name, '-', true);
            $skills[$skillName]['groups'][$name] = [
                'name' => self::SKILL_GROUP_NAMES[$name],
                'sum' => $sum,
                'max' => $max,
                'percentage' => $percentage,
                'text' => $this->textGroup($skillName, $name, $percentage),
            ];
        }

        return [
            'skills' => $skills,
        ];
    }

    private function textGroup(string $skillId, string $groupId, float $percentage): string
    {
        $name = (function () use ($percentage) {
            if ($percentage < 34) {
                return 'minus';
            } elseif ($percentage < 67) {
                return 'normal';
            } else {
                return 'plus';
            }
        })();
        $skill = $this->getXml()->children($skillId);
        $groups = $skill->children('groups');
        $group = $groups->children($groupId);
        $text = $group->children($name);
        return $text->text();
    }

    private function getXml(): Crawler
    {
        if (empty(self::$resultXml)) {
            $filename = $this->kernel->getProjectDir() . "/xml/result/talantum.xml";
            $fileContent = file_get_contents($filename);
            self::$resultXml = new Crawler($fileContent);
        }
        return self::$resultXml;
    }
}