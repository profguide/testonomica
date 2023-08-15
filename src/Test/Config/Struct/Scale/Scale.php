<?php

declare(strict_types=1);

namespace App\Test\Config\Struct\Scale;

/**
 * Шкала прогресса для результата.
 * Например, мы делаем тест с двумя измеряемыми величинами: интроверсия и экстраверсия.
 * Мы хотим разместить в результате две шкалы - по одному на каждый параметр.
 * Для этого нужно указать переменную, содержащую посчитанный процент (@see AutoCalculator) для каждой шкалы.
 * В нашем случае это "VALUES.introvert.percentage", которая содержит процент ответов со значением "introvert"
 * от максимальной суммы возможных ответов.
 * Здесь "VALUES" - это пространство значений. Существуем несколько таких пространств.
 * VALUES - процент от максимальной суммы, которую можно набрать
 * REPEATS - процент от общего кол-ва вопросов
 * Кроме "percentage" существует "percentage_value" - когда нужно знать сколько параметр набрал изолированно,
 * то есть от максимально возможного для этого значения.
 *
 * Остальные значения нужны для текста. Например, "20%", "5 из 10", "Интроверсия: 20%".
 *
 * @property-read $percentVar - variable, representing the percentage value, e.g. VALUES.extravert.percentage
 * @property-read $showVar - variable, representing a number in a view, e.g. VALUES.introvert.sum
 * @property-read $showMaxVal - e.g. 60
 * @property-read $label - e.g. "Ваш уровень агрессии"
 */
final readonly class Scale
{
    public function __construct(
        public string  $percentVar,
        public ?string $showVar,
        public ?int    $showMaxVal,
        public ?string $label)
    {
    }
}