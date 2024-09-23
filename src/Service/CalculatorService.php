<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Service;


use App\Entity\Result;
use App\Entity\Test;
use App\Repository\SourceRepositoryInterface;
use App\Repository\TestRepositoryInterface;
use App\Subscriber\Locale;
use App\Test\AbstractCalculator;
use App\Test\AbstractComplexCalculator;
use App\Test\AnswersHolder;
use App\Test\AnswersSerializer;
use App\Test\CalculatorInterface;
use App\Test\CalculatorNameResolver;
use App\Test\QuestionsHolder;
use App\Test\ResultUtil;
use Symfony\Component\HttpKernel\KernelInterface;

class CalculatorService
{
    public function __construct(
        private CalculatorNameResolver    $calculatorNameResolver,
        private AnswersSerializer         $serializer,
        private SourceRepositoryInterface $sourceRepository,
        private TestRepositoryInterface   $testRepository,
        private KernelInterface           $kernel,
        private Locale                    $locale)
    {
    }

    public function calculate(Result $result): array
    {
        $calculator = ResultUtil::isComplex($result) ?
            $this->initComplexCalculator($result) :
            $this->initSingleCalculator($result);
        return $calculator->calculate();
    }

    private function initComplexCalculator(Result $complexResult): CalculatorInterface
    {
        $calculators = [];
        $jsonResults = json_decode($complexResult->getData(), true);
        foreach ($jsonResults as $testId => $resultData) {
            $result = new Result();
            $result->setTest($this->loadTest($testId));
            $result->setData(json_encode($resultData));
            $calculators[$testId] = $this->initSingleCalculator($result);
        }
        /**@var AbstractComplexCalculator $calculatorName */
        $calculatorName = $this->calculatorName($complexResult);
//        todo a check
//        if (!$calculatorName instanceof AbstractComplexCalculator) {
//            throw new \RuntimeException("The calculator is not a heir of AbstractComplexCalculator.");
//        }
        return new $calculatorName($calculators, $this->kernel);
    }

    private function initSingleCalculator(Result $result): CalculatorInterface
    {
        /**@var AbstractCalculator $calculatorName */
        $calculatorName = $this->calculatorName($result);
        return new $calculatorName(
            new AnswersHolder($this->serializer->deserialize($result->getData())),
            new QuestionsHolder($this->sourceRepository->getAllQuestions($result->getTest())),
            $this->kernel,
            $this->locale->getValue());
    }

    private function calculatorName(Result $result): string
    {
        return $this->calculatorNameResolver->resolveByTest($result->getTest());
    }

    private function loadTest(int $testId): Test
    {
        if (($test = $this->testRepository->findOneById($testId)) == null) {
            throw new \RuntimeException("Test {$testId} not found");
        }
        return $test;
    }
}