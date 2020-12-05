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
use App\Test\AnswersSerializer;
use App\Test\TestStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Stateless API прохождения теста, промежуточные данные хранятся у клиента (браузер, сервисы).
 * Для сохранения @see TestApiStatelessController::saveResults()
 * todo написать какое-то правило, чтобы сделать роут доступным только для профгида.
 * возможно, передача спецтокена во враппер метода api(Request $request)
 * @Route("/tests/cli", name="test_cli.", stateless=true)
 * @package App\Controller
 * @author: adavydov
 * @since: 23.10.2020
 */
class TestApiStatelessController extends TestApiAbstract
{
    const HEADER_STATUS = 'Test-Status';

    /**@var AnswersSerializer */
    private $serializer;

    public function __construct(
        TestService $testService,
        TestSourceService $sourceService,
        ResultService $resultService,
        AnswersSerializer $serializer)
    {
        $this->serializer = $serializer;
        parent::__construct($testService, $sourceService, $resultService);
    }

    /**
     * @Route("/save/{id}/", name="save_results")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function saveResults(int $id, Request $request)
    {
        $test = $this->loadTest($id);
        $answers = $this->serializer->deserialize($request->get('result'));
        $result = $this->resultService->create($test, $answers);
        return new Response($result->getUuid());
    }

    public function end(Test $test)
    {
        return new Response(
            "Обработка результата",
            Response::HTTP_OK,
            [
                'Access-Control-Expose-Headers' => self::HEADER_STATUS,
                self::HEADER_STATUS => TestStatus::finished(),
            ]);
    }

    protected function saveAnswer(Test $test, string $questionId, array $value): void
    {
        return; // no state - not save
    }

    public function clear(Test $test)
    {
        throw new BadRequestHttpException("Unsupported operation");
    }

    public function restore(Test $test)
    {
        throw new BadRequestHttpException("Unsupported operation");
    }

    private function loadTest($id): Test
    {
        if (($test = $this->testService->findById($id)) == null) {
            throw new NotFoundHttpException('Test not found');
        }
        return $test;
    }
}
