<?php

declare(strict_types=1);

namespace App\V2\Progress\Command\Build;

use App\Entity\Answer;
use App\Service\TestSourceService;
use App\Test\Progress\Progress;
use App\Test\Progress\ProgressConverter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @see BuildProgressFromRawData
 * todo test
 */
#[AsMessageHandler]
final readonly class BuildProgressFromRawDataHandler
{
    public function __construct(private TestSourceService $testSourceService)
    {
    }

    public function __invoke(BuildProgressFromRawData $command): Progress
    {
        // при переходе на эту команду старым кодом нужно учесть, что был ещё т.н. legacy (см TestResultRestController::save)
        // его использует студика (вернее видежт.. надо сбилдить)
        // и колледжу нужноп оставить свежий виджет.

        $progress = (new ProgressConverter())->convert($command->data);

        $answers = [];
        foreach ($progress as $qId => $values) {
            $answers[$qId] = self::createAnswer($qId, $values);
        }

        // todo uncomment
//        $this->testSourceService->validateRawAnswers($command->test, $answers);

        return new Progress($answers);
    }

    // todo отдельный объект - тут какие-то преобразования - всё нужно тестировать.
    private static function createAnswer(int $qId, $values): Answer
    {
        return new Answer((string)$qId, is_array($values) ? $values : [$values]);
    }
}