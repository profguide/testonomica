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
 * todo удалить остальные контроллеры и инфраструктуру связанную с хранением прогресса на сервере.
 * todo кешировать вопросы с помощью Redis
 * Можно пойти еще дальше: тест полностью загружается при старте: никакого next и prev на сервере нет.
 */
class TestProgressRestController extends AbstractTestRestController implements AccessTokenAuthenticatedController
{
    /**
     * @Route("/first/{testId<\d+>}/")
     * @param int $testId
     * @return Response
     */
    public function first(int $testId): Response
    {
        $test = $this->getTest($testId);
        return $this->json($this->questionResponseData($test, $this->questions->getFirstQuestion($test)));
    }

    /**
     * @Route("/next/{testId<\d+>}/")
     * @param int $testId
     * @param Request $request
     * @return Response
     */
    public function next(int $testId, Request $request): Response
    {
        $test = $this->getTest($testId);
        $questionId = $this->getRequestParameter($request, 'q');
//        $value = $this->getRequestParameter($request, 'v', false); // взято чтобы провалидировать, так что желательно сделать.
        return $this->json($this->questionResponseData($test, $this->questions->getNextQuestion($test, $questionId)));
    }

    /**
     * @Route("/prev/{testId<\d+>}/")
     * @param int $testId
     * @param Request $request
     * @return Response
     */
    public function prev(int $testId, Request $request): Response
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