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
use App\Test\QuestionsHolder;
use App\Test\SourceRepositoryInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CalculatorService
{
    /**@var AnswersSerializer */
    private $serializer;

    /**@var KernelInterface */
    private $kernel;

    /**@var SourceRepositoryInterface */
    private $sourceRepository;

    const CALCULATORS_NAMESPACE = '\App\Test\Calculator\\';

    public function __construct(
        AnswersSerializer $serializer,
        SourceRepositoryInterface $sourceRepository,
        KernelInterface $kernel)
    {
        $this->serializer = $serializer;
        $this->sourceRepository = $sourceRepository;
        $this->kernel = $kernel;
    }

    public function calculate(Test $test, Result $result): array
    {
        return $this->calculateJson($test, $result->getData());
    }

    public function calculateJson(Test $test, string $data): array
    {
        return ($this->getCalculator($test))->calculate(
            new AnswersHolder($this->serializer->deserialize($data)),
            new QuestionsHolder($this->sourceRepository->getAllQuestions($test)));
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