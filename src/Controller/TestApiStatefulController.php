<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Result;
use App\Entity\Test;
use App\Service\AnswerService;
use App\Service\ResultService;
use App\Service\TestService;
use App\Service\TestSourceService;
use App\Test\AnswersSerializer;
use App\Test\Question;
use App\Test\TestStatus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * @Route("/tests/api", name="tests_api")
 * @package App\Controller
 * @author: adavydov
 * @since: 23.10.2020
 */
class TestApiStatefulController extends TestApiAbstract
{
    /**@var AnswerService */
    private $answerService;

    /**@var ResultService */
    private $resultService;

    /**@var AnswersSerializer */
    private $serializer;

    public function __construct(
        TestService $testService,
        TestSourceService $sourceService,
        AnswerService $answerService,
        ResultService $resultService,
        AnswersSerializer $serializer)
    {
        $this->answerService = $answerService;
        $this->resultService = $resultService;
        $this->serializer = $serializer;
        parent::__construct($testService, $sourceService);
    }

    protected function saveAnswer(Test $test, $questionId, $value): void
    {
        $this->answerService->save($test, Answer::create($questionId, $value));
    }

    public function end(Test $test)
    {
        $answers = $this->answerService->getAll($test);
        $result = Result::create($test, Uuid::v4(), $this->serializer->serialize($answers));
        $this->resultService->save($result);
        $this->resultService->saveSessionResult($result);
        return new Response(
            "Обработка результата",
            Response::HTTP_OK,
            [
                'Access-Control-Expose-Headers' => 'test-status, result-uuid',
                'test-status' => TestStatus::finished(),
                'result-uuid' => $result->getUuid(),
            ]);
    }

    public function clear(Test $test)
    {
        $this->answerService->clear($test);
        $this->resultService->clearSessionResult($test);
        return $this->sourceService->getFirstQuestion($test);
    }

    public function restore(Test $test): ?Question
    {
        if (($lastId = $this->answerService->getLastId($test)) != null) {
            if (($question = $this->sourceService->getNextQuestion($test, $lastId)) != null) {
                return $question;
            } else {
                // подстраховка на случай, когда Result по каким-то причинам не найден, а все вопросы уже отвечены
                // вернем последний вопрос
                return $this->sourceService->getQuestion($test, $lastId);
            }
        } else {
            return $this->sourceService->getFirstQuestion($test);
        }
    }
}