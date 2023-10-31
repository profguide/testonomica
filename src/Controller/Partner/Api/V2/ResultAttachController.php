<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V2;

use App\Controller\AbstractRestController;
use App\Entity\Result;
use App\Exception\ResultNotFoundException;
use App\Repository\ProviderUserRepository;
use App\Repository\ResultRepository;
use App\Test\Result\ResultKeyFactory;
use App\V2\Result\Command\AttachResultToUser;
use App\V2\Result\Exception\ResultAlreadyAttachedAnotherUserException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

final class ResultAttachController extends AbstractRestController
{
    const REQUEST_PARAM_RESULT = 'result_key';
    const REQUEST_PARAM_USER = 'user_key';
    const RESPONSE_STATUS = 'status';

    public function __construct(
        private readonly ResultRepository       $resultRepository,
        private readonly ResultKeyFactory       $resultKeyFactory,
        private readonly ProviderUserRepository $users)
    {
    }

    /**
     * Здесь происходит связывание результата и пользователя.
     *
     * Parameters:
     * - @link self::REQUEST_PARAM_USER - ключ пользователя
     * - @link self::REQUEST_PARAM_RESULT - ключ результата
     */
    #[Route('/partner/api/v2/result/attach')]
    public function attach(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $result = $this->getResultFromRequest($request);
            $user = $this->getUserFromRequest($request);

            $bus->dispatch(new AttachResultToUser($result, $user));
            return $this->json([self::RESPONSE_STATUS => true]);
        } catch (BadRequestException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 400);
        } catch (ResultNotFoundException|UserNotFoundException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 404);
        } catch (HandlerFailedException $e) {
            if (!$e->getPrevious()) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 500);
            }
            try {
                throw $e->getPrevious();
            } catch (ResultAlreadyAttachedAnotherUserException $e) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 412);
            } catch (\Exception $e) {
                return $this->json(['error' => ['message' => $e->getMessage()]], 500);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]], 500);
        }
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

    private function getUserFromRequest(Request $request)
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
}