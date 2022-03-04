<?php
/**
 * @author: adavydov
 * @since: 22.12.2020
 */

namespace App\Test;


use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractCalculator implements CalculatorInterface
{
    protected AnswersHolder $answersHolder;

    protected QuestionsHolder $questionsHolder;

    protected KernelInterface $kernel;

    protected string $locale;

    public function __construct(
        AnswersHolder $answersHolder,
        QuestionsHolder $questionsHolder,
        KernelInterface $kernel,
        string $locale = 'ru')
    {
        $this->answersHolder = $answersHolder;
        $this->questionsHolder = $questionsHolder;
        $this->kernel = $kernel;
        $this->locale = $locale;
    }
}