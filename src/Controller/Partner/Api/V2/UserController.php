<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V2;

use App\Controller\AbstractRestController;
use App\Entity\Provider;
use App\Entity\ProviderUser;
use App\Exception\ProviderNotFoundException;
use App\Repository\ProviderRepository;
use App\V2\Provider\Command\RegisterUser\RegisterProviderUser;
use App\V2\Provider\Policy\Payment\Validator\Exception\PaymentPolicyValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractRestController
{
    const PARAM_CLIENT = 'client';
    const PARAM_USER = 'user_id';

    public function __construct(private readonly ProviderRepository $providers)
    {
    }

    /**
     * todo POST, тк создание позователя это пост, а не вопрос
     * Регистрирует пользователя провайдера и возвращает его идентификатор.
     * Повторно пользователь не создаётся.
     *
     * Результат:
     * - создан новый пользователь или найден старый.
     * - счётчик выданных доступов компании увеличится на 1 если создан новый пользователь.
     * - возвращён ID пользователя (UUID)
     *
     * Parameters:
     * - @link self::PARAM_CLIENT - постоянный токен провайдера
     * - @link self::PARAM_USER - идентификатор пользователя в системе компании
     */
    #[Route('/partner/api/v2/user/register')]
    public function register(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $provider = $this->getClientFromRequest($request);
            $userId = $this->getUserIdFromRequest($request);

            $envelop = $bus->dispatch(new RegisterProviderUser($provider, $userId));
            $handledStamp = $envelop->last(HandledStamp::class);

            /**@var ProviderUser $user */
            $user = $handledStamp->getResult();
            return $this->json(['user_key' => $user->getId()]);
        } catch (BadRequestException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 400);
        } catch (ProviderNotFoundException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 404);
        } catch (PaymentPolicyValidationException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 412);
        } catch (\Exception $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 500);
        }
    }

    private function getClientFromRequest(Request $request): Provider
    {
        $token = $request->get(self::PARAM_CLIENT);

        if (!$token) {
            throw new BadRequestException('The required "' . self::PARAM_CLIENT . '" parameter is missing.');
        }

        $client = $this->providers->getByToken($token);
        if (!$client) {
            throw new ProviderNotFoundException('Client not found with the provided token.');
        }

        return $client;
    }

    private function getUserIdFromRequest(Request $request): string
    {
        $id = $request->get(self::PARAM_USER);

        if (!$id) {
            throw new BadRequestException('The required "' . self::PARAM_USER . '" parameter is missing.');
        }

        return $id;
    }
}