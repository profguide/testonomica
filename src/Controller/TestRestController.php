<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Test;
use App\Repository\TestRepository;
use App\Service\CalculatorService;
use App\Service\ResultService;
use App\Service\TestSourceService;
use App\Test\ResultRenderer;
use App\Test\ResultUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tests/api/v1")
 * , format="json"
 * , stateless=true
 * Этот контроллер должен стать единственным API.
 * Вся инфраструктура, связанная с хранением прогресса на бэкенде должна быть удалена.
 * Можно пойти еще дальше: тест полностью загружается при старте. Никакого next и prev на сервере нет.
 *
 * todo get id instead of value.
 *  Чтобы не прописывать id в каждый вариант ответа можно давать id по правилу questionId-optionIndex, если id нет.
 *
 * - next(currentId, value) - value for validation: {status: progress/finished, question: Question()}
 * - back(currentId): {status: progress, question: Question()}
 * - info(): {name, description, length}
 * - result(progress): {uuid, result}
 *
 * All requests must be marked with the header X-ACCESS-TOKEN and be intercepted somewhere else,
 * Response must include header X-ACCESS-TOKEN with the new one (should be generated and added somewhere else)
 *
 */
class TestRestController extends AbstractController
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
     * @Route("/info/{testId<\d+>}/")
     * @param int $testId
     * @return Response
     */
    public function info(int $testId): Response
    {
        $test = $this->getTest($testId);
        $length = $this->questions->getTotalCount($test);
        return $this->json([
            'name' => $test->getName(),
            'description' => $test->getDescription(),
            'length' => $length
        ]);
//        // cache publicly for 3600 seconds
//        $response->setPublic();
//        $response->setMaxAge(3600);
//        $response->headers->addCacheControlDirective('must-revalidate', true);
    }

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

    private function questionResponseData(Test $test, Question $question): array
    {
        $number = $this->questions->getQuestionNumber($test, $question->getId());
        $length = $count = $this->questions->getTotalCount($test);
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

    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $response = parent::json($data, $status, $headers, $context);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $response;
    }

    private static function createAnswer(int $qId, $values): Answer
    {
        return new Answer((string)$qId, is_array($values) ? $values : [$values]);
    }
}