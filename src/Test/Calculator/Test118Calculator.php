<?php
/**
 * @author: adavydov
 * @since: 14.01.2021
 */

namespace App\Test\Calculator;


use App\Test\AbstractCalculator;
use App\Util\AnswersUtil;

class Test118Calculator extends AbstractCalculator
{
    public function calculate(): array
    {
        $map = [
            'trevoga' => [2, 3, 7, 12, 16, 21, 23, 26, 28, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58],
            'perejivanie' => [5, 10, 15, 20, 24, 30, 33, 36, 39, 42, 44],
            'frustraciya' => [1, 3, 6, 11, 17, 19, 25, 29, 32, 35, 38, 41, 43],
            'samovirajenie' => [27, 31, 34, 37, 40, 45],
            'proverka_znaniy' => [2, 7, 12, 16, 21, 26],
            'ojidaniya' => [3, 8, 13, 17, 22],
            'soprotivlyaemost' => [9, 14, 18, 23, 28],
            'otnosheniya' => [2, 6, 11, 32, 35, 41, 44, 47],
        ];
        // ['trevoga' => 0, ...]
        $result = array_fill_keys(array_keys($map), 0);
        $result['nesovpadenie'] = 0;

        foreach ($this->answersHolder->getAll() as $answer) {
            $id = $answer->getQuestionId();
            if ($answer->hasValues(['da', 'net'])) {
                continue;
            }
            if ($answer->hasValue('nesovpadenie')) {
                $result['nesovpadenie'] += 1;
            }
            foreach ($map as $name => $ids) {
                if (in_array($id, $ids)) {
                    $result[$name] += 1;
                }
            }
        }

        return array_merge($result, [
            'scale' => [
                'trevoga' => round($result['trevoga'] * 100 / 22),
                'perejivanie' => round($result['perejivanie'] * 100 / 11),
                'frustraciya' => round($result['frustraciya'] * 100 / 13),
                'samovirajenie' => round($result['samovirajenie'] * 100 / 6),
                'proverka_znaniy' => round($result['proverka_znaniy'] * 100 / 6),
                'ojidaniya' => round($result['ojidaniya'] * 100 / 5),
                'soprotivlyaemost' => round($result['soprotivlyaemost'] * 100 / 5),
                'otnosheniya' => round($result['otnosheniya'] * 100 / 8),
                'nesovpadenie' => round($result['nesovpadenie'] * 100 / 58),
            ]
        ]);
    }
}