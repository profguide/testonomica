<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V2;

use App\Controller\AbstractRestController;
use App\Entity\ProviderUser;
use App\Entity\Result;
use App\Entity\Test;
use App\Exception\ProgressValidationException;
use App\Exception\TestNotFoundException;
use App\Repository\ProviderUserRepository;
use App\Repository\TestRepository;
use App\Tests\Controller\Partner\Api\V2\ProgressControllerTest;
use App\V2\Progress\Command\Save\SaveProgress;
use App\V2\Progress\RawAnswersToProgressConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

/**
 * @see ProgressControllerTest
 */
final class ProgressController extends AbstractRestController
{
    const REQUEST_PARAM_TEST = 'test';
    const REQUEST_PARAM_USER = 'user_key';
    const REQUEST_PARAM_PROGRESS = 'progress';
    const RESPONSE_PARAM_RESULT_KEY = 'result_key';

    public function __construct(private readonly TestRepository $tests, private readonly ProviderUserRepository $users)
    {
    }

    /**
     * todo POST тк отправка чего-либо - это POST
     * Сохраняет прогресс и возвращает его ключ.
     * Далее с этим ключом можно запросить результат.
     *
     * Результат:
     * - сохранён прогресс
     * - возвращён ключ прогресса
     *
     * Parameters:
     * - @link self::REQUEST_PARAM_TEST - slug теста
     * - @link self::REQUEST_PARAM_USER - ключ пользователя
     * - @link self::REQUEST_PARAM_PROGRESS - список ответов
     */
    #[Route('/partner/api/v2/progress/save')]
    public function save(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $test = $this->getTestFromRequest($request);
            $user = $this->getUserFromRequest($request);
            $answers = $this->getAnswersFromRequest($request);

            $rawAnswersToProgressConverter = new RawAnswersToProgressConverter();
            $progress = $rawAnswersToProgressConverter->convert($answers);

            // Save Progress
            $envelop = $bus->dispatch(new SaveProgress($test, $user, $progress));
            $handledStamp = $envelop->last(HandledStamp::class);
            /**@var Result $result */
            $result = $handledStamp->getResult();

            return $this->json([self::RESPONSE_PARAM_RESULT_KEY => $result->getNewId()]);
        } catch (BadRequestException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 400);
        } catch (TestNotFoundException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 404);
        } catch (HandlerFailedException $e) {
            if (!$e->getPrevious()) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 500);
            }
            try {
                throw $e->getPrevious();
            } catch (ProgressValidationException $e) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 412);
            } catch (\Exception $e) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 500);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 500);
        }
    }

    private function getTestFromRequest(Request $request): Test
    {
        $slug = $request->get(self::REQUEST_PARAM_TEST);

        if (!$slug) {
            throw new BadRequestException('The required "' . self::REQUEST_PARAM_TEST . '" parameter is missing.');
        }

        $test = $this->tests->findOneBySlug($slug);
        if (!$test) {
            throw new TestNotFoundException("Test not found with the provided \"" . self::REQUEST_PARAM_TEST . "\" value: \"$slug\".");
        }

        return $test;
    }

    private function getUserFromRequest(Request $request): ProviderUser
    {
        $key = $request->get(self::REQUEST_PARAM_USER);

        if (!$key) {
            throw new BadRequestException('The required "' . self::REQUEST_PARAM_USER . '" parameter is missing.');
        }

        if (!Uuid::isValid($key)) {
            throw new BadRequestException('Invalid "' . self::REQUEST_PARAM_USER . '", it should be correct uuid type.');
        }

        $user = $this->users->find($key);
        if (!$user) {
            throw new UserNotFoundException('User not found with the provided key.');
        }

        return $user;
    }

    private function getAnswersFromRequest(Request $request): array
    {
        $answers = $request->get(self::REQUEST_PARAM_PROGRESS);

        if (!$answers) {
            throw new BadRequestException('The required "' . self::REQUEST_PARAM_PROGRESS . '" parameter is missing.');
        }

        if (!is_array($answers)) {
            throw new BadRequestException('The "' . self::REQUEST_PARAM_PROGRESS . '" parameter must be in array format.');
        }

        return $answers;
    }
}