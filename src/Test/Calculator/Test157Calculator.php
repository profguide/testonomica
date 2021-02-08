<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test157Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $sums = AnswersUtil::sumValuesMap($this->questionsHolder, $this->answersHolder);
        $jelatelnost = AnswersUtil::arraySum($sums, 'jelatelnost');
        $jz = AnswersUtil::arraySum($sums, 'jz');
        $jg = AnswersUtil::arraySum($sums, 'jg');
        $jag = AnswersUtil::arraySum($sums, 'jag');
        $aggression = AnswersUtil::arraySum($sums, 'aggression');
        $an = AnswersUtil::arraySum($sums, 'an');
        $ar = AnswersUtil::arraySum($sums, 'ar');
        $ass = AnswersUtil::arraySum($sums, 'ass');
        $asnr = AnswersUtil::arraySum($sums, 'asnr');
        $samopovrejdenie = AnswersUtil::arraySum($sums, 'samopovrejdenie');
        $sr = AnswersUtil::arraySum($sums, 'sr');
        $sn = AnswersUtil::arraySum($sums, 'sn');
        $gipersocialnoe = AnswersUtil::arraySum($sums, 'gipersocialnoe');
        $gr = AnswersUtil::arraySum($sums, 'gr');
        $zavisimoe = AnswersUtil::arraySum($sums, 'zavisimoe');
        $zn = AnswersUtil::arraySum($sums, 'zn');
        $zr = AnswersUtil::arraySum($sums, 'zr');
        $nekritichnoe = AnswersUtil::arraySum($sums, 'nekritichnoe');
        $nr = AnswersUtil::arraySum($sums, 'nr');
        $realizovannoe = AnswersUtil::arraySum($sums, 'realizovannoe');
        $values = [
            'jelatelnost' => $jelatelnost + $zr + $jg + $jag,
            'aggression' => $aggression + $an + $ar + $ass + $asnr + $jag,
            'samopovrejdenie' => $samopovrejdenie + $ass + $sr + $sn + $asnr,
            'gipersocialnoe' => $gipersocialnoe + $jg + $gr + $jag,
            'zavisimoe' => $zavisimoe + $jz + $zn + $zr,
            'nekritichnoe' => $nekritichnoe + $nr + $an + $zn + $sn + $asnr,
            'realizovannoe' => $realizovannoe + $nr + $zr + $gr + $sr + $asnr + $ar
        ];
        return array_merge($values, [
            'scale' => [
                'jelatelnost' => round($values['jelatelnost'] * 100 / 9),
                'aggression' => round($values['aggression'] * 100 / 27),
                'samopovrejdenie' => round($values['samopovrejdenie'] * 100 / 22),
                'gipersocialnoe' => round($values['gipersocialnoe'] * 100 / 14),
                'zavisimoe' => round($values['zavisimoe'] * 100 / 19),
                'nekritichnoe' => round($values['nekritichnoe'] * 100 / 19),
                'realizovannoe' => round($values['realizovannoe'] * 100 / 18),
            ]
        ]);
    }
}