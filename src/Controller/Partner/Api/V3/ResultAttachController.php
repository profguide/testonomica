<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3;

use App\Controller\AbstractRestController;
use App\Entity\Provider;
use App\Entity\ProviderUser;
use App\Entity\Result;
use App\Exception\ProviderNotFoundException;
use App\Exception\ResultNotFoundException;
use App\Repository\ProviderRepository;
use App\Repository\ResultRepository;
use App\Test\Result\ResultKeyFactory;
use App\V2\Provider\Command\RegisterUser\RegisterProviderUser;
use App\V2\Provider\Policy\Payment\Validator\Exception\PaymentPolicyValidationException;
use App\V2\Result\Command\AttachResultToUser;
use App\V2\Result\Exception\ResultAlreadyAttachedAnotherUserException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final class ResultAttachController extends AbstractRestController
{
    const REQUEST_PARAM_PROVIDER = 'client_key';
    const REQUEST_PARAM_USER = 'user_id';
    const REQUEST_PARAM_RESULT = 'result_key';
    const RESPONSE_STATUS = 'status';

    public function __construct(
        private readonly ProviderRepository $providers,
        private readonly ResultRepository   $resultRepository,
        private readonly ResultKeyFactory   $resultKeyFactory)
    {
    }

    /**
     * Здесь происходит связывание результата и пользователя.
     * - регистрируем пользователя (или находим)
     * - связываем результат с пользователем
     *
     * Parameters:
     * - @link self::REQUEST_PARAM_PROVIDER - ключ провайдера
     * - @link self::REQUEST_PARAM_USER - id пользователя системы провайдера (id, email, sess_id etc.)
     * - @link self::REQUEST_PARAM_RESULT - ключ результата
     */
    #[Route('/partner/api/v3/result/attach')]
    public function attach(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $provider = $this->getProviderFromRequest($request);
            $userId = $this->getUserIdFromRequest($request);
            $result = $this->getResultFromRequest($request);

            // зарегистрируем/найдём пользователя
            $envelop = $bus->dispatch(new RegisterProviderUser($provider, $userId));
            $handledStamp = $envelop->last(HandledStamp::class);
            /**@var ProviderUser $user */
            $user = $handledStamp->getResult();

            // привяжем пользователя к результату
            $bus->dispatch(new AttachResultToUser($result, $user));
            return $this->json([self::RESPONSE_STATUS => true]);
        } catch (BadRequestException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 400);
        } catch (ResultNotFoundException|UserNotFoundException|ProviderNotFoundException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 404);
        } catch (HandlerFailedException $e) {
            if (!$e->getPrevious()) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 500);
            }
            try {
                throw $e->getPrevious();
            } catch (PaymentPolicyValidationException|ResultAlreadyAttachedAnotherUserException $e) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 412);
            } catch (\Exception $e) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 500);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 500);
        }
    }

    private function getProviderFromRequest(Request $request): Provider
    {
        $key = $request->get(self::REQUEST_PARAM_PROVIDER);

        if (!$key) {
            throw new BadRequestException('The required "' . self::REQUEST_PARAM_PROVIDER . '" parameter is missing.');
        }

        $client = $this->providers->getByToken($key);
        if (!$client) {
            throw new ProviderNotFoundException('Client not found with the provided key.');
        }

        return $client;
    }

    private function getResultFromRequest(Request $request): Result
    {
        $key = $request->get(self::REQUEST_PARAM_RESULT);

        if (!$key) {
            throw new BadRequestException('The required "' . self::REQUEST_PARAM_RESULT . '" parameter is missing.');
        }

        $resultKey = $this->resultKeyFactory->create($key);
        $result = $this->resultRepository->findByKey($resultKey);

        if (!$result) {
            throw new ResultNotFoundException("Result not found with provided key.");
        }

        return $result;
    }

    private function getUserIdFromRequest(Request $request): string
    {
        $id = $request->get(self::REQUEST_PARAM_USER);

        if (!$id) {
            throw new BadRequestException('The required "' . self::REQUEST_PARAM_USER . '" parameter is missing.');
        }

        return $id;
    }
}