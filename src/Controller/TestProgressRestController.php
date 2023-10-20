<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tests/api/v1", format="json")
 */
class TestProgressRestController extends AbstractTestRestController
{
    /**
     * @Route("/first/{testId<[\w-]+>}/")
     * @param string $testId
     * @return Response
     */
    public function first(string $testId): Response
    {
        $test = $this->getTest($testId);
        return $this->json($this->questionResponseData($test, $this->questions->getFirstQuestion($test)));
    }

    /**
     * @Route("/next/{testId<[\w-]+>}/")
     * @param string $testId
     * @param Request $request
     * @return Response
     */
    public function next(string $testId, Request $request): Response
    {
        $test = $this->getTest($testId);
        $questionId = $this->getRequestParameter($request, 'q');
//        $value = $this->getRequestParameter($request, 'v', false); // взято чтобы провалидировать, так что желательно сделать.
        $question = $this->questions->getNextQuestion($test, $questionId);
        if (!$question) {
            throw new \LogicException('No more questions.');
        }
        return $this->json($this->questionResponseData($test, $question));
    }

    /**
     * @Route("/prev/{testId<[\w-]+>}/")
     * @param string $testId
     * @param Request $request
     * @return Response
     */
    public function prev(string $testId, Request $request): Response
    {
        $test = $this->getTest($testId);
        $questionId = $this->getRequestParameter($request, 'q');
        return $this->json($this->questionResponseData($test, $this->questions->getPrevQuestion($test, $questionId)));
    }

    private function questionResponseData(Test $test, Question $question): array
    {
        $number = $this->questions->getQuestionNumber($test, $question->getId());
        $length = $this->questions->getTotalCount($test);
        return [
            'question' => $question,
            'number' => $number,
            'length' => $length,
        ];
    }

    private function getRequestParameter(Request $request, string $name, bool $isRequired = true)
    {
        $value = $request->get($name);
        if ($value == null && $isRequired) {
            throw new \InvalidArgumentException("Parameter $name is required.");
        }
        return $value;
    }
}