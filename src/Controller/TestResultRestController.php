<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Test;
use App\Repository\TestRepository;
use App\Service\CalculatorService;
use App\Service\ResultService;
use App\Service\TestSourceService;
use App\Test\ResultRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tests/api/v1")
 */
class TestResultRestController extends RestController
{
    private TestRepository $tests;

    private TestSourceService $questions;

    private ResultService $resultService;

    private CalculatorService $calculatorService;

    private ResultRenderer $resultRenderer;

    public function __construct(
        TestRepository $tests,
        TestSourceService $questions,
        ResultService $resultService,
        CalculatorService $calculatorService,
        ResultRenderer $resultRenderer)
    {
        $this->tests = $tests;
        $this->questions = $questions;
        $this->resultService = $resultService;
        $this->calculatorService = $calculatorService;
        $this->resultRenderer = $resultRenderer;
    }

    /**
     * @Route("/save/{testId<\d+>}/")
     * todo think of moving it to a separate controller as only this rare action consumes creating a heavy ResultService.
     * @param int $testId
     * @param Request $request
     * @return Response
     */
    public function save(int $testId, Request $request): Response
    {
        $test = $this->getTest($testId);
        $progress = $this->getRequestJsonParameter($request, 'progress');
        $answers = [];
        foreach ($progress as $qId => $values) {
            $answers[$qId] = self::createAnswer($qId, $values);
        }
        $this->questions->validateRawAnswers($test, $answers);
        $result = $this->resultService->create($test, $answers);

        return $this->json(['key' => $result->getUuid()]);
    }

    /**
     * @Route("/result/")
     * todo think of moving it to a separate controller as only this rare action consumes creating a heavy ResultService.
     * @param Request $request
     * @return Response
     */
    public function result(Request $request): Response
    {
        $key = $this->getRequestParameter($request, 'key');
        $result = $this->resultService->findByUuid($key);
        $data = $this->calculatorService->calculate($result);
        // todo как вернуть результат?
        //  можно html, но бутстраповские стили... а если всё в iframe? тогда можно бутстрап, но не будут работать стили внешние
        //  можно html с минимальным набором бутстрепа - отступы. То есть на тестономике, допустим, бутстреп подхватится, а для внешней интеграции нет - можно переопределить кастомно.
        //  можно в json, но тогда react должен уметь обрабатывать каждый результат по своему, что не годится.
        //  можно html, но чисто результат - без внешних тегов, чтобы сразу шел <p> и заканчивался </p>. - это лучший вариант.
        //      допустим, последний вариант. тогда нужно превратить все эти 102.html.twig в пассивов, чтобы они загружались, а не их.
        //      кстати, они могли бы внутри себя определять дополнительные стили, что хорошо может пригодиться для теста на профориентацию.
        //
        //  а что если скилбокс скажет, что хочет получать разбитый json? нет проблем - мы можем сделать какой-то резолвер.
        //  на основании теста и компании мы можем возвращать что они сами пожелают.
        //  а можно сделать экшн настраиваемым, чтобы можно было передать тип контента.
        //  Однако, это может быть не так просто. Потому что обычный тест содержит массу всякого - ul, table, ссылки, картинки.
        //  Можно некоторые тесты делать такими, мы ведь сделали профориентационный тест таким. Ну вот.

        // можно рассмотреть два вида интеграции
        //  - iframe - полностью наши стили, то есть фактически сайт в сайте только без меню и подвала.
        //  - div - вариант для тестономики, профгида, и всех тех, кто хочет полностью управлять стилями.
        $test = $result->getTest();
        return $this->resultRenderer->render($test, $data);
    }

    private function getRequestParameter(Request $request, string $name, bool $isRequired = true)
    {
        $value = $request->get($name);
        if ($value == null && $isRequired) {
            throw new \InvalidArgumentException("Parameter $name is required.");
        }
        return $value;
    }

    private function getRequestJsonParameter(Request $request, string $name)
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data[$name])) {
            throw new \InvalidArgumentException("Parameter $name is required.");
        }
        return $data[$name];
    }

    private function getTest(int $id): Test
    {
        $test = $this->tests->findOneById($id);
        if (!$test) {
            throw new NotFoundHttpException();
        }
        return $test;
    }

    private static function createAnswer(int $qId, $values): Answer
    {
        return new Answer((string)$qId, is_array($values) ? $values : [$values]);
    }
}