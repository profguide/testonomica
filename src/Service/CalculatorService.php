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
use Symfony\Component\HttpKernel\KernelInterface;

class CalculatorService
{
    /**@var AnswersSerializer */
    private $serializer;

    /**@var KernelInterface */
    private $kernel;

    const CALCULATORS_NAMESPACE = '\App\Test\Calculator\\';

    public function __construct(AnswersSerializer $serializer, KernelInterface $kernel)
    {
        $this->serializer = $serializer;
        $this->kernel = $kernel;
    }

    public function calculate(Test $test, Result $result): array
    {
        return $this->calculateJson($test, $result->getData());
    }

    public function calculateJson(Test $test, string $data): array
    {
        $answersHolder = new AnswersHolder($this->serializer->deserialize($data));
        return ($this->getCalculator($test))->calculate($answersHolder);
    }

    private function getCalculator(Test $test): CalculatorInterface
    {
        $calculatorName = self::CALCULATORS_NAMESPACE . ucfirst($this->resolveCalculatorName($test)) . 'Calculator';
        return new $calculatorName($this->kernel);
    }

    private function resolveCalculatorName(Test $test): string
    {
        return $test->getCalculatorName() ?? $test->getId();
    }
}