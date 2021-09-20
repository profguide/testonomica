<?php

declare(strict_types=1);

namespace App\Test\Calculator;

use App\Entity\Answer;
use App\Entity\Question;
use App\Test\AbstractCalculator;
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

    private const MIN = 1;
    private const NORM = 2;
    private const MAX = 3;

    private static $answersGroupBasedXml;

    private static $answersQuestionBasedXml;

    function calculate(): array
    {
        $groupsSums = [];
        $groupsCount = [];

        foreach ($this->answersHolder->getAll() as $answer) {
            $question = $this->questionsHolder->get($answer->getQuestionId());
            preg_match("/(\w+)-(\w+)/", $question->getVariety(), $skills);
            if (count($skills) != 3) {
                throw new \RuntimeException("Unsupported group name: {$question->getVariety()}.");
            }
            $leftSkillName = $skills[1];
            $rightSkillName = $skills[2];

            // count groups for counting percent
            if (!isset($groupsCount[$leftSkillName])) {
                $groupsCount[$leftSkillName] = self::MAX;
            } else {
                $groupsCount[$leftSkillName] += self::MAX;
            }
            if (!isset($groupsCount[$rightSkillName])) {
                $groupsCount[$rightSkillName] = self::MAX;
            } else {
                $groupsCount[$rightSkillName] += self::MAX;
            }

            // total sum for a group
            if (!isset($groupsSums[$leftSkillName])) {
                $groupsSums[$leftSkillName] = 0;
            }
            if (!isset($groupsSums[$rightSkillName])) {
                $groupsSums[$rightSkillName] = 0;
            }

            $value = $answer->getValue()[0];

            switch ($value) {
                case 0:
                    $groupsSums[$leftSkillName] += self::MAX;
                    break;
                case 1:
                    $groupsSums[$leftSkillName] += self::NORM;
                    break;
                case 2:
                    $groupsSums[$leftSkillName] += self::MAX;
                    $groupsSums[$rightSkillName] += self::MAX;
                    break;
                case 3:
                    $groupsSums[$rightSkillName] += self::NORM;
                    break;
                case 4:
                    $groupsSums[$rightSkillName] += self::MAX;
                    break;
            }
        }

        $result = [];

        arsort($groupsSums);
        foreach ($groupsSums as $name => $value) {
            $result[$name] = [
                'name' => self::SKILL_NAMES[$name],
                'value' => $value,
                'max' => $groupsCount[$name],
                'percentage' => round($value * 100 / $groupsCount[$name])
                // 9 - x
                // 5 - 100
            ];
        }

        return [
            'skills' => $result
        ];


//        $skills = [];
//        foreach ($this->questionsHolder->byGroupsMask() as $name => $skill) {
//            $skillQuestionHolder = new QuestionsHolder($skill);
//            $sum = AnswersUtil::sum($skillQuestionHolder, $this->answersHolder);
//            $max = AnswersUtil::max($skillQuestionHolder);
//            $skills[$name] = [
//                'name' => self::SKILL_NAMES[$name],
//                'total' => [
//                    'sum' => $sum,
//                    'max' => $max,
//                    'percentage' => round($sum * 100 / $max)
//                ],
//            ];
//        }
//        $questionBasedGroupText = [];
//        foreach ($this->questionsHolder->getAll() as $question) {
//            $groupName = $question->getVariety();
//            if (!isset($questionBasedGroupText[$groupName])) {
//                $questionBasedGroupText[$groupName] = [];
//            }
//            $questionBasedGroupText[$groupName][$question->getId()] =
//                $this->textQuestionBased($question, $this->answersHolder->get((string)$question->getId()));
//        }
//
//        foreach ($this->questionsHolder->byGroups() as $name => $group) {
//            $groupQuestionHolder = new QuestionsHolder($group);
//            $sum = AnswersUtil::sum($groupQuestionHolder, $this->answersHolder);
//            $max = AnswersUtil::max($groupQuestionHolder);
//            $percentage = round($sum * 100 / $max);
//            $skillName = strstr($name, '-', true);
//            $skills[$skillName]['groups'][$name] = [
//                'name' => self::SKILL_GROUP_NAMES[$name],
//                'sum' => $sum,
//                'max' => $max,
//                'percentage' => $percentage,
//                'text' => [
//                    'group_based' => $this->textGroupBased($skillName, $name, $percentage),
//                    'question_based' => $questionBasedGroupText[$name],
//                ],
//            ];
//        }
//
//        return [
//            'skills' => $skills,
//        ];
    }

    private function textGroupBased(string $skillId, string $groupId, float $percentage): string
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
        $skill = $this->getGroupAnswersXml()->children($skillId);
        $groups = $skill->children('groups');
        $group = $groups->children($groupId);
        $text = $group->children($name);
        return $text->text();
    }

    private function getGroupAnswersXml(): Crawler
    {
        if (empty(self::$answersGroupBasedXml)) {
            $filename = $this->kernel->getProjectDir() . "/xml/talantum/answers_group_based.xml";
            $fileContent = file_get_contents($filename);
            self::$answersGroupBasedXml = new Crawler($fileContent);
        }
        return self::$answersGroupBasedXml;
    }

    // ----

    private function textQuestionBased(Question $question, Answer $answer): string
    {
        $name = (function () use ($answer) {
            if ($answer->getValue()[0] == -1) {
                return 'minus';
            } elseif ($answer->getValue()[0] == 0) {
                return 'normal';
            } else {
                return 'plus';
            }
        })();

        $skill = $this->getQuestionAnswersXml()->children("id-{$question->getId()}");
        return $skill->children($name)->text();
    }

    private function getQuestionAnswersXml(): Crawler
    {
        if (empty(self::$answersQuestionBasedXml)) {
            $filename = $this->kernel->getProjectDir() . "/xml/talantum/answers_question_based.xml";
            $fileContent = file_get_contents($filename);
            self::$answersQuestionBasedXml = new Crawler($fileContent);
        }
        return self::$answersQuestionBasedXml;
    }
}