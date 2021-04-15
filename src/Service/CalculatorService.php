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
use App\Test\AbstractCalculator;
use App\Test\AbstractComplexCalculator;
use App\Test\AnswersHolder;
use App\Test\AnswersSerializer;
use App\Test\CalculatorInterface;
use App\Test\QuestionsHolder;
use App\Test\ResultUtil;
use Symfony\Component\HttpKernel\KernelInterface;

class CalculatorService
{
    private AnswersSerializer $serializer;

    private SourceRepositoryInterface $sourceRepository;

    private TestRepositoryInterface $testRepository;

    private KernelInterface $kernel;

    const CALCULATORS_NAMESPACE = '\App\Test\Calculator\\';

    public function __construct(
        AnswersSerializer $serializer,
        SourceRepositoryInterface $sourceRepository,
        TestRepositoryInterface $testRepository,
        KernelInterface $kernel)
    {
        $this->serializer = $serializer;
        $this->sourceRepository = $sourceRepository;
        $this->testRepository = $testRepository;
        $this->kernel = $kernel;
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
            $this->kernel);
    }

    private function calculatorName(Result $result): string
    {
        $test = $result->getTest();
        // todo determine if it is auto
        // Можно так
        // Выбор калькулятора из списка, где Auto стоит первым.
        // Если не устраивает, то вписываем своё: Test131Calculator, ProforientationTeenCalculator
        // То есть имеем два поля: calculator (на выбор) и customCalculator (альтернативный)
        $name = $test->getCalculatorName() ?? 'Auto';
//        $name = $test->getCalculatorName() ?? 'Test' . $test->getId();
        return self::CALCULATORS_NAMESPACE . ucfirst($name) . 'Calculator';
    }

    private function loadTest(int $testId): Test
    {
        if (($test = $this->testRepository->findOneById($testId)) == null) {
            throw new \RuntimeException("Test {$testId} not found");
        }
        return $test;
    }
}