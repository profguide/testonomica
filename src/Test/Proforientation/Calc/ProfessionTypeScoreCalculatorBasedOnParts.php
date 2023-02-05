<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;
use App\Tests\Test\Proforientation\Calc\ProfessionTypeScoreCalculatorBasedOnPartsTest;

/**
 * ## Принципы
 * 1. Нельзя проставить профессиям силу типа по двум причинам:
 *  а) может оказаться ситуация, когда человек в принципе имеет малую силу ко всему - допустим ниже 50%.
 *     и тогда мы не сможем ему дать никакую профессию.
 *  б) невозомжно объективно оценить профессию - вот матиематик - это 80% или 100%? А может 50%? А остальные профессии?
 *
 * 2. Нельзя сравнивать силу_набранных_типов и доли_участия_типа_в_профессии.
 *  Силу нужно преобразовать в долю и уже тогда сравнивать.
 *  Что значит преобразовать силу в долю?
 *      Допустим, я набрал {math: 100 и art: 50}.
 *      И это вполне нормально, что для меня доля моих интересов и умений такая - {math: 66.6% и art: 33.3%}.
 *      То есть мне лечге даётся и более инетесна система таблиц, чем творчество именно в отношении 3:1.
 *      Если бы они были равны, то получилось бы 1:1.
 *      Если задуматься, то это кажется логичным.
 *  Вот теперь можно легко сравнить путём деления: делим сколько_надо на сколько_получилось.
 *  Так мы получили коэффициент отличия в долях.
 *  А всё-таки, для чего сравнивать доли_участия_набранных_типов и доли_участия_типа_в_профессии?
 *      Потому что оптимально - это когда и профессия и человек совпадают.
 *      Чем точнее совпадение, тем меньше получается коэффициент.
 *
 * ## Как устроена логика подсчёта
 * Пользователь набирает процент силы.
 * В профессии стоит долевой процент участия (НЕ СИЛА!)
 * Силу и доли сравнивать нельзя, поэтому силу указанныз типов приводим к долям.
 * Дальше уже сравниваем доли простым делением. Получается что-то типа коэффициента совпадения.
 * Дальше коэффициент умножаем на силу. Потому что сила тоже важна - это фактор хотения/умения - высокий или низкий сами о себе много говорят.
 *
 * @see ProfessionTypeScoreCalculatorBasedOnPartsTest
 */
final class ProfessionTypeScoreCalculatorBasedOnParts
{
    private array $userTypes;

    public function __construct(array $userTypes)
    {
        $this->userTypes = $userTypes;
    }

    public function calculate(Types $types, TypesCombination $not): Score
    {
        $max = new Score(0);
        foreach ($types->combinations() as $comb) {
            $score = $this->scoreCombination($comb, $not);
            if ($score->value() > $max->value()) {
                $max = $score;
            }
        }

        return $max;
    }

    /**
     * @param TypesCombination $types - доли профессии
     * @param TypesCombination $not - не учитывать комбинации, где присутствуют опредённые типы,
     * пригождается, чтобы отсечь профессии, не требовательные к сложным навыками, когда человек их набрал.
     * Например, слесарь - только body, а человек набрал и body и human и it. Если в профессии указано not="human",
     * то рейтинг будет 0
     * @return Score
     */
    private function scoreCombination(TypesCombination $types, TypesCombination $not): Score
    {
        // если набранный тип указан в $not, профессия не подходит
        $top = array_slice($this->userTypes, 0, 4);
        foreach (array_keys($top) as $type) {
            if (in_array($type, $not->values())) {
                return new Score(0);
            }
        }

        // отфильтруем типы пользователя: только которые есть в профессии
        $userTypes = array_filter($this->userTypes, function ($name) use ($types) {
            return array_key_exists($name, $types->values());
        }, ARRAY_FILTER_USE_KEY);

        // приведём значения пользователя (силу) к долевому участию
        // это понадобится, чтобы сравнивать доли с долями
        $userTypeParts = self::toParts($userTypes);

        $sum = 0;

//        foreach ($userTypeParts as $name => $value) {
//            $profTypeValue = $types->values()[$name];
//
//            $sum += $userTypes[$name];
//            $diff = $profTypeValue - $value;
//            if ($diff < 0) {
//                $sum -= $diff * $diff;
//            }
//        }

        foreach ($userTypeParts as $name => $userTypePart) {
            $profTypePart = $types->values()[$name];
            $userAbsoluteValue = $userTypes[$name];

            // расчитаем отношение требуемой доли к набранной (надо 100, набрали 50 - значит 2)
            if ($userTypePart > 0) {
                $partRatio = $profTypePart / $userTypePart;
//                if ($partRatio > 1) {
//                    $partRatio = 1;
//                }
            } else {
                $partRatio = 0;
            }

            // умножим получившийся коэффицинт на набранный процент.
            $a1 = $partRatio * $userAbsoluteValue;
            $sum += $a1;
        }

        //  надбавка за сложность
        if (count($types->values()) > 1) {
            $sum += count($types->values()) * 10; // 10 / 20 / 30 / 40 / 50
            $max = count($types->values()) * 100; // сумма не может быть больше, чем n*100
            if ($sum > $max) {
                $sum = $max;
            }
        }

        $score = $sum / count($types->values());

        return new Score(round($score, 2), $types->values());
    }

    /**
     * Превращает значения в доли
     * @param array $userTypes
     * @return array
     */
    private static function toParts(array $userTypes): array
    {
        $userTypeParts = [];

        // сумма значений для полсчёта доли.
        $sum = array_sum($userTypes);
        if ($sum == 0) { // not "===" because array_sum might return float
            $sum = 1;
        }
        foreach ($userTypes as $name => $value) {
            $userTypeParts[$name] = round(($value * 100) / $sum, 2);
        }

        return $userTypeParts;
    }
}