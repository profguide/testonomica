<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;

/**
 * Старый алгоритм, основанный на понятии "Топ" типов.
 * Использовалось с ~07.2021 по 01.2023. Жалобы были, но немного. Мы в целом были довольны этим алгоритмом.
 * Но возникали ситуации, которые показывали, что алгоритм топорный. Дело в том, что "топ" - это неясный термин.
 * Мы так и не поняли что такое топ - то ли 3, то ли 4, то ли 5...
 * Может быть первые несколько наиболее близких к вернему? А насколько близких?
 * А если первый высокий, а остальные низкие? Получится, что в топе только один.
 * Для соблюдения этого условия приходилось каждой профессии проставить несколько наборов,
 * в котором хотя бы один - с одним типом. Иначе выходили случаи, когда не подходила ни одна профессия.
 * Так, один человек с высокой математикой и коммуникацией и неплохим art - получил первые несколько профессий
 * художников и дизайнеров. Всё потому что, алгоритм определил в топы art наравне с математикой и коммуникацией
 * и выдал те профессии, где есть хотя бы один art - ведь один средний art выше, чем профессии,
 * где сразу много высоко-средне-низких.
 *
 * После этого мы начали искать другую формулу, и это привело к алгоритму,
 * в коротом топов больше нет @see ProfessionTypeScoreCalculatorBasedOnParts
 *
 * @see ProfessionTypeScoreCalculatorTest
 */
final class ProfessionTypeScoreCalculatorBasedOnTopTypes
{
    private array $userTypes;

    public function __construct(array $userTypes)
    {
        $this->userTypes = $userTypes;
    }

    public function calculate(Types $types, TypesCombination $not): float
    {
        $max = 0;
        foreach ($types->combinations() as $comb) {
            $rating = $this->scoreCombination($comb, $not);
            if ($rating > $max) {
                $max = $rating;
            }
        }

        return (float)$max;
    }

    /**
     * @param TypesCombination $types
     * @param TypesCombination $not
     * @return float
     */
    private function scoreCombination(TypesCombination $types, TypesCombination $not): float
    {
        // Топовые типы
        $userTypes = (new TopTypesCalculator)->calc($this->userTypes);

        // если не набраны все требуемые типы, то это не подходит
        $keysTypesScored = array_keys($userTypes);
        foreach ($types->values() as $name => $value) {
            if (!in_array($name, $keysTypesScored)) {
                return 0;
            }
        }

        // если набранный тип указан в $not, профессия не подходит
        foreach (array_keys($userTypes) as $typeScored) {
            if (array_key_exists($typeScored, $not->values())) {
                return 0;
            }
        }

        // сложим значения набранных типов
        $sum = 0;
        foreach ($userTypes as $type => $value) {
            if (array_key_exists($type, $types->values())) {
                $sum += $value;
            }
        }

        // среднее арифметическое
        return $sum;
    }
}