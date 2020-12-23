<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Test\Calculator;

use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class PersonIntelCalculator extends AbstractCalculator
{
    function calculate(): array
    {
        $groups = $this->questionsHolder->byGroups();
        $sums = [];
        foreach ($groups as $k => $group) {
            $sums[$k] = AnswersUtil::sum($group, $this->answersHolder);
        }
        $result = [
            'creator' => round($sums['creator'] * 100 / 36),
            'leader' => round($sums['leader'] * 100 / 39),
            'communicator' => round($sums['communicator'] * 100 / 14),
            'pretension' => [
                'potential' => round($sums['pretension-potential'] * 100 / 12),
                'avoid' => round($sums['pretension-avoid'] * 100 / 8),
                'competition' => round($sums['pretension-competition'] * 100 / 8),
                'initiative' => round($sums['pretension-initiative'] * 100 / 8),
                'expect' => round($sums['pretension-expect'] * 100 / 8),
                'effort' => round($sums['pretension-effort'] * 100 / 12),
                'total' => round(
                    ($sums['pretension-potential'] +
                        $sums['pretension-avoid'] +
                        $sums['pretension-competition'] +
                        $sums['pretension-initiative'] +
                        $sums['pretension-expect'] +
                        $sums['pretension-effort']) * 100 / 56 // 56 is a sum of maximum values for the questions
                )
            ],
            'businessman' => [
                'freedom' => round($sums['business-freedom'] * 100 / 4),
                'stable' => round($sums['business-stable'] * 100 / 8),
                'responsibility' => round($sums['business-responsibility'] * 100 / 12),
                'risk' => round($sums['business-risk'] * 100 / 8),
                'search' => round($sums['business-search'] * 100 / 4),
                'total' => round(
                    ($sums['business-freedom'] +
                        $sums['business-stable'] +
                        $sums['business-responsibility'] +
                        $sums['business-risk'] +
                        $sums['business-search']) * 100 / 36),
            ],
            'temperament' => [
                'fleg' => round($sums['temp-fleg'] * 100 / 6),
                'mel' => round($sums['temp-mel'] * 100 / 6),
                'hol' => round($sums['temp-hol'] * 100 / 6),
                'san' => round($sums['temp-san'] * 100 / 6),
            ],
            'confidence' => round($sums['confidence'] * 100 / 5),
            'empathy' => round($sums['empathy'] * 100 / 13),
            'iq' => [
                'verbal' => round($sums['iq-verbal'] * 100 / 10),
                'logic' => round($sums['iq-logic'] * 100 / 10),
                'math' => round($sums['iq-math'] * 100 / 10),
                'spatial' => round($sums['iq-spatial'] * 100 / 4),
                'attention' => round($sums['iq-attention'] * 100 / 2),
                'abstract' => round($sums['iq-abstract'] * 100 / 10),
                'erudition' => round($sums['iq-erudition'] * 100 / 10),
                'total' => round(
                    ($sums['iq-verbal'] +
                        $sums['iq-logic'] +
                        $sums['iq-math'] +
                        $sums['iq-spatial'] +
                        $sums['iq-attention'] +
                        $sums['iq-abstract'] +
                        $sums['iq-erudition']) * 100 / 56)
            ],
            'system_values' => AnswersUtil::ratingToTextArray(
                $this->questionsHolder->get(350),
                $this->answersHolder->get(350)),
        ];

        // todo compare sums and result - count number of groups ничего ли не забыл

        return $result;
    }
}