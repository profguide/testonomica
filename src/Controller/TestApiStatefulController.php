<?php

namespace App\Controller;

use App\Entity\Test;
use App\Test\Answer;
use http\Exception\RuntimeException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tests/api", name="tests_api")
 * @package App\Controller
 * @author: adavydov
 * @since: 23.10.2020
 */
class TestApiStatefulController extends TestApiAbstract
{
    protected function saveAnswer(Test $test, $questionId, $value): void
    {
//        $answerValue = $request->get('answer');
//        $questionId = $request->get('question');
        $answer = Answer::create($questionId, $value);
        throw new RuntimeException("Saving is not unsupported yet");
    }

    public function end(Test $test)
    {
        // todo
        throw new RuntimeException("Ending is not unsupported yet");
//        return new Response(
//            "Обработка результата",
//            Response::HTTP_OK,
//            [
//                'Access-Control-Expose-Headers' => 'Test-Status',
//                'Test-Status' => TestStatus::finished()
//            ]);
    }

    public function clear(Test $test)
    {
        return $this->sourceService->getFirstQuestion($test);
    }

    public function restore(Test $test)
    {
        return $this->sourceService->getFirstQuestion($test);
    }

//    /**
//     * @Route("/stateless", name=".stateless", stateless=true)
//     * @param Request $request
//     * @return Response
//     */
//    public function stateless(Request $request)
//    {
//        return $this->render('tests/question.html.twig', $this->api($request));
//    }
//
//    /**
//     * @Route("/", name=".stateful")
//     * @param Request $request
//     * @return Response
//     */
//    public function stateful(Request $request)
//    {
//        $operationName = $this->operationByRequest($request);
//        self::validateAnswerFormat($operationName, $request);
//        $test = $this->loadTestByRequest($request);
//        $question = null;
//        if ($operationName == self::OPERATION_NEXT) {
//            $question = $this->next($test, $request->get('question'));
////            $question = $this->sourceService->getNextQuestion($test, $questionId);
//            if (!$question) {
//                return new Response(
//                    "Обработка результата",
//                    Response::HTTP_OK,
//                    [
//                        'Access-Control-Expose-Headers' => 'Test-Status',
//                        'Test-Status' => TestStatus::finished()
//                    ]);
//            }
//        } elseif ($operationName == self::OPERATION_BACK) {
//            $question = $this->back($test, $request->get('question'));
////            $question = $this->sourceService->getPrevQuestion($test, $questionId);
////            if (!$question) {
////                $question = $this->sourceService->getFirstQuestion($test);
////            }
//        } elseif ($operationName == self::OPERATION_CLEAR) {
//            $question = $this->first($test);
////            $question = $this->sourceService->getFirstQuestion($test);
//        } elseif ($operationName == self::OPERATION_RESTORE) {
//            $question = $this->restore($test);
////            $question = $this->sourceService->getFirstQuestion($test);
//        } else {
//            throw new RuntimeException("Unknown operation");
//        }
//
////        /**@var QuestionFlowDao $dao */
////        $dao = $this->api($request);
////        if ($dao['operation'] == self::OPERATION_NEXT) {
////            $this->answersService->save($dao['test'], self::answerFromRequest($request));
////        }
//////        if ($dao['progress'] == $dao['count']) {
//////            $uuid = $this->resultService->save($this['test'], $this->answersService->getAll());
//////            $this->redirect($uuid);
//////        }
////        return $this->render('tests/question.html.twig', $dao);
//    }
//
//    private function api(Request $request)
//    {
//        // определим тип операции
//        $operationName = $this->operationByRequest($request);
//        // провалидизируем формат
//        self::validateAnswerFormat($operationName, $request);
//        // найдем тест
//        $test = $this->loadTestByRequest($request);
//        $questionId = $request->get('question');
//        $question = null;
//        if ($operationName == self::OPERATION_NEXT) {
////            $time_start = microtime(true);
//            $question = $this->sourceService->getNextQuestion($test, $questionId);
////            echo microtime(true) - $time_start;
////            dd($question);
//            if (!$question) {
//                return new Response(
//                    "Обработка результата",
//                    Response::HTTP_OK,
//                    [
//                        'Access-Control-Expose-Headers' => 'Test-Status',
//                        'Test-Status' => TestStatus::finished()
//                    ]);
//            }
//        } elseif ($operationName == self::OPERATION_BACK) {
//            $question = $this->sourceService->getPrevQuestion($test, $questionId);
//            if (!$question) {
//                $question = $this->sourceService->getFirstQuestion($test);
//            }
//            // todo clear and restore remove to stateful
//        } elseif ($operationName == self::OPERATION_CLEAR) {
//            $question = $this->sourceService->getFirstQuestion($test);
//        } elseif ($operationName == self::OPERATION_RESTORE) {
//            $question = $this->sourceService->getFirstQuestion($test);
//        } else {
//            throw new RuntimeException("Unknown operation");
//        }
//        $count = $this->sourceService->getTotalCount($test);
//        $progress = $this->sourceService->getQuestionNumber($test, $question);
//        return [
//            'operation' => $operationName,
//            'test' => $test,
//            'question' => $question,
//            'count' => $count,
//            'progress' => $progress,
//            'percent' => $progress * 100 / $count,
//        ];
//    }
}