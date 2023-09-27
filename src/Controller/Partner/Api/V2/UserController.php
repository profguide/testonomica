<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V2;

use App\Controller\AbstractRestController;
use App\Entity\Provider;
use App\Entity\ProviderUser;
use App\Repository\ProviderRepository;
use App\V2\Provider\Command\RegisterUser\RegisterProviderUser;
use App\V2\Provider\Policy\Payment\Validator\Exception\PaymentPolicyValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\ProviderNotFoundException;

final class UserController extends AbstractRestController
{
    public function __construct(private readonly ProviderRepository $providers)
    {
    }

    /**
     * Регистрирует пользователя провайдера и возвращает его идентификатор.
     * Повторно пользователь не создаётся.
     *
     * Результат:
     * - создан новый пользователь или найден старый.
     * - счётчик выданных доступов компании увеличится на 1 если создан новый пользователь.
     * - возвращён ID пользователя (UUID)
     *
     * Example:
     * ?client=partner_token&user_id=123123
     *
     * Параметры:
     * - client - постоянный токен провайдера
     * - user_id - идентификатор пользователя в системе компании
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
        } catch (BadRequestException $exception) {
            return $this->json(['error' => ['message' => $exception->getMessage()]], 400);
        } catch (ProviderNotFoundException $exception) {
            return $this->json(['error' => ['message' => $exception->getMessage()]], 404);
        } catch (PaymentPolicyValidationException $exception) {
            return $this->json(['error' => ['message' => $exception->getMessage()]], 412);
        } catch (\Exception $exception) {
            return $this->json(['error' => ['message' => $exception->getMessage()]], 500);
        }
    }

    private function getClientFromRequest(Request $request): Provider
    {
        $token = $request->get('client');

        if (!$token) {
            throw new BadRequestException('The required "client" parameter is missing.');
        }

        $client = $this->providers->getByToken($token);
        if (!$client) {
            throw new ProviderNotFoundException('Client not found with the provided token.');
        }

        return $client;
    }

    private function getUserIdFromRequest(Request $request): string
    {
        $id = $request->get('user_id');

        if (!$id) {
            throw new BadRequestException('The required "user_id" parameter is missing.');
        }

        if (empty($id)) {
            throw new BadRequestException('The required "user_id" parameter is empty.');
        }

        return $id;
    }
}