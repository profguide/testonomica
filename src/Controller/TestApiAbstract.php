<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Controller;


use App\Entity\Test;
use App\Service\ResultService;
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
    const OPERATION_START = 'start';

    const OPERATION_NEXT = 'next';

    const OPERATION_BACK = 'back';

    const OPERATION_CLEAR = 'clear';

    const OPERATION_RESTORE = 'restore';

    /**@var TestService */
    protected $testService;

    /**@var ResultService */
    protected $resultService;

    /**@var TestSourceService */
    protected $sourceService;

    public function __construct(TestService $testService, TestSourceService $sourceService, ResultService $resultService)
    {
        $this->testService = $testService;
        $this->sourceService = $sourceService;
        $this->resultService = $resultService;
    }

    /**
     * @Route("/", name="api")
     * @param Request $request
     * @return Response
     */
    public function api(Request $request)
    {
        $test = $this->loadTestByRequest($request);
        $operationName = $this->operationByRequest($request);
        self::assertOperationFormat($operationName, $request);
        if ($operationName == self::OPERATION_START) {
            $question = $this->first($test);
        } elseif ($operationName == self::OPERATION_NEXT) {
            $question = $this->next($test, $this->grabQuestion($request));
            $this->saveAnswer($test, $this->grabQuestion($request), $this->grabAnswer($request));
            if (!$question) {
                return $this->end($test);
            }
        } elseif ($operationName == self::OPERATION_BACK) {
            $question = $this->back($test, $this->grabQuestion($request));
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
            'percent' => ($progress - 1) * 100 / $count,
        ]);
    }

    protected abstract function saveAnswer(Test $test, string $questionId, array $value): void;

    protected abstract function clear(Test $test);

    protected abstract function restore(Test $test);

    protected abstract function end(Test $test);

    private static function assertOperationFormat(string $operationName, Request $request)
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
        } elseif ($request->get('start')) {
            return self::OPERATION_START;
        } else {
            return self::OPERATION_NEXT;
        }
    }

    private function loadTestById(int $id): Test
    {
        if (($test = $this->testService->findById($id)) == null) {
            throw new NotFoundHttpException($id);
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

    private function back(Test $test, string $questionId)
    {
        return $this->sourceService->getPrevQuestion($test, $questionId) ?? $this->sourceService->getFirstQuestion($test);
    }

    private function next($test, string $questionId)
    {
        return $this->sourceService->getNextQuestion($test, $questionId);
    }

    private function first($test)
    {
        return $this->sourceService->getFirstQuestion($test);
    }

    private function grabAnswer(Request $request): array
    {
        $answer = $request->get('answer');
        if (is_array($answer)) {
            return $answer;
        } else {
            return [$answer];
        }
    }

    private function grabQuestion(Request $request)
    {
        return $request->get('question');
    }
}