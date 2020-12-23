<?php
/**
 * @author: adavydov
 * @since: 22.12.2020
 */

namespace App\Test;


use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractCalculator implements CalculatorInterface
{
    /**@var AnswersHolder */
    protected $answersHolder;

    /**@var QuestionsHolder */
    protected $questionsHolder;

    /**@var KernelInterface */
    protected $kernel;

    public function __construct(
        AnswersHolder $answersHolder,
        QuestionsHolder $questionsHolder,
        KernelInterface $kernel)
    {
        $this->answersHolder = $answersHolder;
        $this->questionsHolder = $questionsHolder;
        $this->kernel = $kernel;
    }
}