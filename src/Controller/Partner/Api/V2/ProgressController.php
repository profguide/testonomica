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
use App\V2\Progress\Command\Save\SaveProgress;
use App\V2\Progress\RawAnswersToProgressConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

final class ProgressController extends AbstractRestController
{
    const PARAM_TEST = 'test';
    const PARAM_USER = 'user_key';
    const PARAM_PROGRESS = 'progress';

    public function __construct(private readonly TestRepository $tests, private readonly ProviderUserRepository $users)
    {
    }

    /**
     * todo POST тк отправка чего-либо - это POST
     * Здесь происходит сохранение прогресса и выдача его ключа.
     *
     * Клиентский сайт передаёт: {USER_KEY}, slug теста, прогресс пользователя.
     * 1. Просходит поиск Политики тестов.
     * 2. Политика тестов проверяет возможность сохранить результат.
     *    Если отказано, возвращаем код ошибки (создать код и сообщение).
     *    Политика_Проверки использует в своей работе учётную таблицу
     *    ManyToMany: USER_TOKEN & RESULT_KEY & TEST_ID для проверки.
     * 3. Происходит сохранение прогресса.
     * 4. Происходит запись в учётную таблицу ProviderUserResult: USER_KEY & RESULT_KEY & TEST_ID
     *
     * 5. Возвращается ключ сохранённого прогресса.
     *
     * Далее с этим ключом клиентский сайт может запрашивать результат многократно в любом формате
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

            return $this->json(['result_key' => $result->getUuid()]);
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
        $slug = $request->get(self::PARAM_TEST);

        if (!$slug) {
            throw new BadRequestException('The required "' . self::PARAM_TEST . '" parameter is missing.');
        }

        $test = $this->tests->findOneBySlug($slug);
        if (!$test) {
            throw new TestNotFoundException("Test not found with the provided value \"$slug\".");
        }

        return $test;
    }

    private function getUserFromRequest(Request $request): ProviderUser
    {
        $key = $request->get(self::PARAM_USER);

        if (!$key) {
            throw new BadRequestException('The required "' . self::PARAM_USER . '" parameter is missing.');
        }

        if (!Uuid::isValid($key)) {
            throw new BadRequestException('Invalid "' . self::PARAM_USER . '", it should be correct uuid type.');
        }

        $user = $this->users->find($key);
        if (!$user) {
            throw new TestNotFoundException('User not found with the provided key.');
        }

        return $user;
    }

    private function getAnswersFromRequest(Request $request): array
    {
        $answers = $request->get(self::PARAM_PROGRESS);

        if (!$answers) {
            throw new BadRequestException('The required "' . self::PARAM_PROGRESS . '" parameter is missing.');
        }

        if (!is_array($answers)) {
            throw new BadRequestException('The "' . self::PARAM_PROGRESS . '" parameter must be in array format.');
        }

        return $answers;
    }
}