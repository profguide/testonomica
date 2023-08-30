<?php

declare(strict_types=1);

namespace App\Test\Calculator;

use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

final class TestodromSexualOrientationCalculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->questionsHolder, $this->answersHolder);

        // clean excess values
        $allowedKeys = ['hetero', 'homo', 'bi', 'asexual'];
        $values = array_filter($sums, function ($name) use ($allowedKeys) {
            return in_array($name, $allowedKeys);
        }, ARRAY_FILTER_USE_KEY);

        // special case: question #18: add 0.5
        if ($this->answersHolder->get('18')->hasValue('yes')) {
            $values['homo'] += 0.5;
            $values['bi'] += 0.5;
        }

        // special case: question #20: add 0.5
        if ($this->answersHolder->get('20')->hasValue('yes')) {
            $values['homo'] += 0.5;
            $values['bi'] += 0.5;
        } elseif ($this->answersHolder->get('20')->hasValue('no')) {
            $values['hetero'] += 0.5;
        } else { // yes-no
            $values['asexual'] += 0.5;
        }

        return [
            'hetero' => [
                'sum' => $values['hetero'],
                'percentage' => round($values['hetero'] * 100 / 20)
            ],
            'homo' => [
                'sum' => $values['homo'],
                'percentage' => round($values['homo'] * 100 / 20)
            ],
            'bi' => [
                'sum' => $values['bi'],
                'percentage' => round($values['bi'] * 100 / 20)
            ],
            'asexual' => [
                'sum' => $values['asexual'],
                'percentage' => round($values['asexual'] * 100 / 20)
            ],
        ];
    }
}