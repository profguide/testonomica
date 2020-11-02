<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Controller;


use App\Entity\Test;
use App\Service\TestService;
use App\Service\TestSourceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

abstract class TestApiAbstract extends AbstractController
{
    const OPERATION_NEXT = 'next';

    const OPERATION_BACK = 'back';

    const OPERATION_CLEAR = 'clear';

    const OPERATION_RESTORE = 'restore';

    /**@var TestService */
    protected $testService;

    /**@var TestSourceService */
    protected $sourceService;

    public function __construct(TestService $testService, TestSourceService $sourceService)
    {
        $this->testService = $testService;
        $this->sourceService = $sourceService;
    }

    /**
     * @Route("/", name="api")
     * @param Request $request
     * @return Response
     */
    public function api(Request $request)
    {
        $operationName = $this->operationByRequest($request);
        self::validateAnswerFormat($operationName, $request);
        $test = $this->loadTestByRequest($request);
        $question = null;
        if ($operationName == self::OPERATION_NEXT) {
            $questionId = $request->get('question');
            $answerValue = $request->get('answer');
            $question = $this->sourceService->getNextQuestion($test, $questionId);
            $this->saveAnswer($test, $questionId, $answerValue);
            if (!$question) {
                return $this->end($test);
            }
        } elseif ($operationName == self::OPERATION_BACK) {
            $questionId = $request->get('question');
            $question = $this->sourceService->getPrevQuestion($test, $questionId);
            if (!$question) {
                $question = $this->sourceService->getFirstQuestion($test);
            }
        } elseif ($operationName == self::OPERATION_CLEAR) {
            $question = $this->clear($test);
        } elseif ($operationName == self::OPERATION_RESTORE) {
            $question = $this->restore($test);
        } else {
            throw new BadRequestHttpException("Unknown operation");
        }
        $count = $this->sourceService->getTotalCount($test);
        $progress = $this->sourceService->getQuestionNumber($test, $question);
        return $this->render('tests/question.html.twig', [
            'operation' => $operationName,
            'test' => $test,
            'question' => $question,
            'count' => $count,
            'progress' => $progress,
            'percent' => $progress * 100 / $count,
        ]);
    }

    protected abstract function saveAnswer(Test $test, string $questionId, string $value): void;

    protected abstract function clear(Test $test);

    protected abstract function restore(Test $test);

    protected abstract function end(Test $test);

    private static function validateAnswerFormat(string $operationName, Request $request)
    {
        $question = $request->get('question');
        $answer = $request->get('answer');
        if ($operationName === self::OPERATION_NEXT) {
            if (empty($question) or $answer == null) {
                throw new BadRequestHttpException("Next operation requires \"question\" and \"answer\" be passed");
            }
        } elseif ($operationName === self::OPERATION_BACK) {
            if (empty($question)) {
                throw new BadRequestHttpException("Back operation requires \"question\" be passed");
            }
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    private function operationByRequest(Request $request): string
    {
        if ($request->get("back")) {
            return self::OPERATION_BACK;
        } elseif ($request->get("clear")) {
            return self::OPERATION_CLEAR;
        } elseif ($request->get("restore")) {
            return self::OPERATION_RESTORE;
        } else {
            return self::OPERATION_NEXT;
        }
    }

    private function loadTestById(int $id): Test
    {
        if (($test = $this->testService->findById($id)) == null) {
            throw new NotFoundHttpException();
        }
        return $test;
    }

    private function loadTestByRequest(Request $request): Test
    {
        if (($id = $request->get("test")) == null) {
            throw new BadRequestHttpException("\"test\" parameter is required");
        }
        return $this->loadTestById($id);
    }
}