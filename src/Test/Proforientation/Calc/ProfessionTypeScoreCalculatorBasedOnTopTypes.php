<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\Proforientation\Types;
use App\Test\Proforientation\TypesCombination;
use App\Tests\Test\Proforientation\Calc\ProfessionTypeScoreCalculatorBasedOnTopTypesTest;

/**
 * Алгоритм, основанный на понятии "Топ" типов.
 * Бывают жалобы, но совсем не так много, как было в первой версии теста - нас тогда просто закидали помидорами.
 * В целом мы довольны этим алгоритмом.
 * Но возникают ситуации, которые показывали, что алгоритм немного топорный.
 * Дело в том, что "топ" - это не очень неясный термин.
 * Мы так и не поняли что такое топ - то ли 3, то ли 4, то ли 5...
 * Может быть первые несколько наиболее близких к вернему? А насколько близких?
 * А если первый высокий, а остальные низкие? Получится, что в топе только один. Это правильно?
 * Для соблюдения последнего условия приходится каждой профессии проставить несколько наборов,
 * в котором хотя бы один - с одним типом. Иначе бывают случаи, когда не подходит ни одна профессия.
 * Следующая проблема в том, что один человек с высокой математикой и коммуникацией и неплохим art -
 * получит первые несколько профессий художников и дизайнеров.
 * Всё потому что, алгоритм определит в топы art наравне с математикой и коммуникацией
 * и выдаст те профессии, где есть хотя бы один art - ведь один средний art выше, чем профессии,
 * где сразу много высоко-средне-низких.
 * (эту проблему я попробовал решить с помощью системы надбавки за сложность комбинации -
 *  и это видимо неплохо работает, об этом ниже)
 *
 * После этого мы начали искать другую формулу, и это привело к алгоритму,
 * в коротом ТОПа больше нет @see ProfessionTypeScoreCalculatorBasedOnParts,
 * а всесто этого используется понятие ДОЛЯ. Доля типа в профессии.
 * Однако алгоритм основанный на долях нам показался не таким точным или как по крайней мере не повысил точности.
 * Он тоже не лишен недостатков, таких как простые комбинации наверху, а сложные внизу. Имеет свои нерешенные проблемы.
 * При этом он накладывает необходимость указывать проценты, опять-таки основанные на нашем субъективном понимании профессии.
 * С ТОПами проще, а результат не хуже.
 *
 * После этого я придумал систему надбавок за сложность комбинации, которая неплохо решает проблему простых/сложных комбинаций.
 * Это такая проблема, когда простая комбинация оказывается выше, чем сложная, потому что никто не набирает все типы на 100%.
 * Система надбавок компенсирует строгость фильтра.
 *
 * Думаю, что дальше нужно попробовать сделать алгоритм основанный на системе отклонений от нормы, когда значение типа - это норма.
 * Психолог - {com: 50, human: 50, natural: 30} - com и human нужно средне, а natural необязательно столько.
 * Это не пропорция, не треуемая сила, а процент отклонения. Надо подумать ещё.
 * Всё на 50%. Лишь только тем профессиям, где показатель нужен реально больше - мы увеличим слегка - как тонкий способ манипуляции.
 *
 * @see ProfessionTypeScoreCalculatorBasedOnTopTypesTest
 */
final class ProfessionTypeScoreCalculatorBasedOnTopTypes
{
    private array $userTypes;

    public function __construct(array $userTypes, private readonly array $subTypes)
    {
        // Топовые типы
        $this->userTypes = (new TopTypesCalculator)->calc($userTypes);
    }

    public function calculate(Types $types, TypesCombination $not): Score
    {
        $max = new Score(0);

        foreach ($types->combinations() as $comb) {
//            $rating = $this->scoreCombination($comb, $not);
            $rating = $this->scoreCombinationComplexAward($comb, $not);
            if ($rating->value() > $max->value()) {
                $max = $rating;
            }
        }

        return $max;
    }

    private function scoreCombination(TypesCombination $types, TypesCombination $not): Score
    {
        // если не набраны все требуемые типы, то это не подходит
        $keysTypesScored = array_keys($this->userTypes);
        foreach ($types->values() as $name => $value) {
            if (!in_array($name, $keysTypesScored)) {
                return new Score(0);
            }
        }

        // если набранный тип указан в $not, профессия не подходит
        foreach (array_keys($this->userTypes) as $typeScored) {
            if (in_array($typeScored, $not->values())) {
                return new Score(0);
            }
        }

        // сложим значения набранных типов
        $sum = 0;
        foreach ($this->userTypes as $type => $value) {
            if (array_key_exists($type, $types->values())) {
                $sum += $value;
            }
        }

        // среднее арифметическое
        return new Score($sum, $types->values());
    }

    /**
     * Алгоритм с надбавкой за сложность комбинации.
     * Это попытка решить проблему сложных и простых профессий (больше/меньше типов). Ниже пояснение.
     *
     * Зачем?
     * Больше типов в профессии означает более строгий фильтр и меньше шансов оказаться наверху.
     * Ведь мы высчитываем среднее значение для комбинации.
     * Например, мама набрала {human: 100, com: 80}.
     * Наверху оказываются профессии с одним типом, например, Философ (human), для которой получается 100% совпадение.
     * А психолог идёт ниже, потому что он получил 90% (расчёт: (100+80)/2=90).
     * В предыдущем алгоритме эта проблему решалась тем, что мы возвращали сумму очков, а не среднее по типам.
     * Так, например, психолог был 180, а философ 100.
     * И действительно, наверх стали вылезать сложные комбинации - ведь в них суммарно оказываестя больше очков.
     * Однако это привело к противоположной проблеме - простые комбинации ушли совсем вниз.
     * Это тоже нехорошо. Потому что Философ с его одним типом не хуже, чем Психолог с двумя.
     *
     * Здесь я пытаюсь решить эту проблему - уравновесить простые и сложные комбинации.
     *
     * Как?
     * 1. Считаем среднее - чтобы количество типов перестало иметь значение.
     * 2. За сложность надбавляем бонус - таким образом компенсируем строгость фильтра.
     * Философ получит 100, а психолог получит тоже 100, если com не 100, но близок к высоте.
     *
     * @param TypesCombination $types
     * @param TypesCombination $not
     * @return Score
     */
    private function scoreCombinationComplexAward(TypesCombination $types, TypesCombination $not): Score
    {
        // если не набраны все требуемые типы, то это не подходит
        $keysTypesScored = array_keys($this->userTypes);
        foreach ($types->values() as $name => $value) {
            if (!in_array($name, $keysTypesScored)) {
                return new Score(0);
            }
        }

        // если набранный тип указан в $not, профессия не подходит
        foreach (array_keys($this->userTypes) as $typeScored) {
            if (in_array($typeScored, $not->values())) {
                return new Score(0);
            }
        }

        // сложим значения набранных типов
        $sum = 0;
        foreach ($this->userTypes as $type => $value) {
            if (array_key_exists($type, $types->values())) {
                $sum += $value;
            }
        }

        //  надбавка за сложность
        $award = ComplexTypeAwardCalculator::calculate($types);
        $sum += $award;

        $score = $sum / count($types->values());

        /**
         * убавка за недобор подтипа
         * подтип - это art::viz или art::muz
         * @see UserSubtypesCalculator
         */
        foreach ($this->subTypes as $typeName => $subtypes) {
            // профессия должна иметь такой же тип и подтип
            $professionSubtypeName = $types->values()[$typeName] ?? null;
            if ($professionSubtypeName) {
                $value = $subtypes[$professionSubtypeName] ?? 1; // значение должно быть точно, но на всякий случай подстрахуемся
                if ($value < 1) {
                    $score = $score * $value;
                }
            }
        }

        return new Score(round($score, 2), [$types->values(), 'award' => $award]);
    }
}