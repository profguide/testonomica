<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Service;


use App\Entity\Result;
use App\Entity\Test;
use App\Test\AnswersHolder;
use App\Test\AnswersSerializer;
use App\Test\CalculatorInterface;

class CalculatorService
{
    /**@var AnswersSerializer */
    private $serializer;

    const CALCULATORS_NAMESPACE = '\App\Test\Calculator\\';

    public function __construct(AnswersSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function calculate(Test $test, Result $result): array
    {
        $answersHolder = new AnswersHolder($this->serializer->deserialize($result->getData()));
        return ($this->getCalculator($test))->calculate($answersHolder);
    }

    private function getCalculator(Test $test): CalculatorInterface
    {
        $calculatorName = self::CALCULATORS_NAMESPACE . ucfirst($this->resolveCalculatorName($test)) . 'Calculator';
        return new $calculatorName();
    }

    private function resolveCalculatorName(Test $test): string
    {
        return $test->getCalculatorName() ?? $test->getId();
    }
}