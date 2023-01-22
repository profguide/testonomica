<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\AnswersHolder;
use App\Test\Proforientation\TypesCombination;
use App\Test\QuestionsHolder;
use App\Util\AnswersUtil;

/**
 * @todo test
 */
final class UserTypesCalculator
{
    private QuestionsHolder $questionsHolder;

    private AnswersHolder $answersHolder;

    public function __construct(QuestionsHolder $questionsHolder, AnswersHolder $answersHolder)
    {
        $this->questionsHolder = $questionsHolder;
        $this->answersHolder = $answersHolder;
    }

    public function calculate(): CalculationTypesValues
    {
        $result = new CalculationTypesValues();

        foreach (TypesCombination::ALL as $name) {
            $result->add($name, new Values(
                $this->calculateUserType("{$name}-force"),
                $this->calculateUserType("{$name}-interest"),
                $this->calculateUserType("{$name}-skills")
            ));
        }

        return $result;
    }

    /**
     * Высчитывает сумму положительных и правильных ответов для группы вопросов
     * @param string $groupName e.g. tech-skills
     * @return float
     */
    private function calculateUserType(string $groupName): float
    {
        $questions = $this->questionsHolder->group($groupName);
        $count = count($questions);
        $rightSum = AnswersUtil::sum(new QuestionsHolder($questions), $this->answersHolder);
        $this->applyExtraAnswers($groupName, $this->answersHolder, $rightSum, $count);

        return (round($rightSum / $count * 100));
    }

    /**
     * Применяет дополнительные правила подсчета
     * @param string $groupName
     * @param AnswersHolder $answersHolder
     * @param int $rightSum
     * @param int $count
     */
    private function applyExtraAnswers(string $groupName, AnswersHolder $answersHolder, int &$rightSum, int &$count)
    {
        if ($groupName == 'it-force') {
            $questionIds = [102];

            foreach ($questionIds as $questionId) {
                $rightSum += $answersHolder->getValuesSum((string)$questionId);
            }
            $count += count($questionIds);
        }
    }
}