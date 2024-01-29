<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Calc;

use App\Test\AnswersHolder;
use App\Test\Proforientation\TypesCombination;
use App\Test\QuestionsHolder;

/**
 * Подтипы
 *
 * подтипы призваны дополнительно влиять на сортировку профессий
 * подтипы являются дополнением к типу и не оказывают влияния на рейтинг типа.
 * подтипы являются дочками типа: {viz, muz}
 * подтипы являются друг другу оппозицией (если одно в плюсе, значит другое в минусе)
 * победивший подтип всегда "1", а проигравший - градация от 0 до n<1.
 * подтипы скрыты в вариантах ответов
 * например, вопрос типа art, и такие варианты ответов: "art::viz", "art::muz", "1", "0".
 * ответ "art::viz" означает viz++ и art++, а ответ "1" означает просто art++
 *
 * Концепция
 * Идея - не возвышать профессии, а наоборот пессимизировать те профессии, подтип которых в минусе.
 * Почему не возвышать? Потому что это может поломать общий рейтинг профессий (art+viz > body).
 * Почему пессимизировать? Потому что подтипы являются оппозицией друг к другу, а это означает,
 * что если выиграл muz, то viz проиграл. Выиграл - значит остался в ряду с другими, а проиграл - ушёл (как в суде).
 *
 * Пример.
 * Победил art::muz, и значит art::viz проиграл.
 * Рассмотрим борьбу иллюстратора, музыканта и писателя.
 * Писатель не хуже и не лучше потому что мы не предусматривали вопросов (подтипов) для писателя.
 * Значит писатель не может проигрывать. Проигрывать могут только подсудные. Подсудные - иллюстратор и музыкант.
 * В нашему примере art::viz проиграл. Значит иллюстратор делает шаг назад.
 *
 * Требования:
 * Победивший подтип - всегда "1", а проигравший - имеет разную степень отдаления (наказания).
 * При равных набранных все подтипы не хуже и не лучше друг друга (требуется протестировать идею).
 * 4 и 4 = {1, 1}
 * 0 и 0 = {1, 1} или {0, 0}
 * 1 и 1 = {1, 1} или {0.25, 0.25}
 * 2 и 1 = {1, 0.75} (если считать от наибольшего набранного) или {1, 0.25} (если считать от максимально возможного)
 * 3 и 1 = {1, 0.3} (если считать от наибольшего набранного) или {1, 0.25} (если считать от максимально возможного)
 * 4 и 1 = {1, 0.25}
 * 4 и 0 = {1, 0}
 */
final readonly class UserSubtypesCalculator
{
    public function __construct(private QuestionsHolder $questionsHolder, private AnswersHolder $answersHolder)
    {
    }

    public function calculate(): array
    {
        // расчитаем набранные суммы значений подтипов
        $values = $this->summarize();

        // найдём максимальные значения для подтипов
        $maximums = $this->maximums();

        // сформируем список типов с вложенными подтипами
        $types = [];
        foreach ($values as $subtypeName => $value) {
            $typename = TypesCombination::SUB[$subtypeName];
            $types[$typename] = array_merge($types[$typename] ?? [], [
                $subtypeName => [
                    'value' => $value,
                    'max' => $maximums[$subtypeName]
                ]
            ]);
        }

        // расчитаем значения в соответствие с требованиями внутри типа
        $percentage = [];
        foreach ($types as $typename => $subtypes) {
            // найдём наибольшее значение для типа
            $typeValues = array_column($subtypes, 'value');
            $biggestValue = max($typeValues);

            $typePercentage = [];

            // посчитаем каждый подтип
            foreach ($subtypes as $name => $params) {
                $value = $params['value'];
                if ($value < $biggestValue) {
                    // высчитаем процент относительно наибольшего значения
                    // можно высчитывать процент от максимально возможного значения или от наибольшего из набранных.
                    // получается различный эффект.
                    // если считать процент от максимального возможного значения...
                    // если считать процент от наибольшего из набранных, то получается более плавный эффект.
                    $percent = (float)($value / $biggestValue);
                } else {
                    // наибольший - всегда победитель
                    $percent = 1;
                }

                $typePercentage[$name] = $percent;
            }

            $percentage[$typename] = $typePercentage;
        }

        return $percentage;
    }

    /**
     * Высчитывает набранные суммы значений подтипов
     * @return array
     */
    private function summarize(): array
    {
        $subtypes = array_fill_keys(array_keys(TypesCombination::SUB), 0);

        foreach ($this->answersHolder->getAll() as $answer) {
            if (count($answer->getValue()) == 1 && $answer->getValue()[0] !== null && str_starts_with($answer->getValue()[0], 'type::')) {
                $value = $answer->getValue()[0];
                $name = str_replace('type::', '', $value);
                $subtypes[$name] = $subtypes[$name] + 1;
            }
        }

        return $subtypes;
    }

    /**
     * Высчитывает максимальные значения для подтипов в тесте
     * Вообще в рамках типа все подтипы должны иметь одинаковое максимальное значение
     * На случай если это изменится - узнаём всё про каждый тип в отдельности
     * @return array ['viz' => 4, 'muz' => 4]
     */
    private function maximums(): array
    {
        $maximums = array_fill_keys(array_keys(TypesCombination::SUB), 0);

        // составим список ответов, являющиеся подтипами
        $awaitingValues = [];
        foreach (array_keys(TypesCombination::SUB) as $key) {
            $awaitingValues[] = "type::$key";
        }

        // правило: один подтип может быть максимум один раз в вопросе
        foreach ($this->questionsHolder->getAll() as $question) {
            // типы, уже посчитанные в текущем вопросе - чтобы не считать более одного раза
            $questionTypes = [];
            foreach ($question->getItems() as $item) {
                $value = $item->getValue();
                if (in_array($value, $awaitingValues) && !in_array($value, $questionTypes)) {
                    $name = str_replace('type::', '', $value);
                    $maximums[$name] = $maximums[$name] + 1;
                    $questionTypes[] = $value;
                }
            }
        }

        return $maximums;
    }
}