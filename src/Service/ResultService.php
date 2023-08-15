<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Service;


use App\Entity\Result;
use App\Entity\Test;
use App\Repository\ResultRepository;
use App\Test\AnswersSerializer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class ResultService
{
    private const SESSION_RESULT_NAME = 'results';

    private ResultRepository $repository;

    private RequestStack $requestStack;

    private AnswersSerializer $serializer;

    public function __construct(
        RequestStack      $requestStack,
        ResultRepository  $repository,
        AnswersSerializer $serializer)
    {
        $this->repository = $repository;
        $this->requestStack = $requestStack;
        $this->serializer = $serializer;
    }

    public function save(Result $result): Result
    {
        return $this->repository->save($result);
    }

    public function findByUuid(string $uuid): ?Result
    {
        return $this->repository->findByUuid($uuid);
    }

    public function saveSessionResult(Result $result): void
    {
        $resultsString = $this->requestStack->getSession()->get(self::SESSION_RESULT_NAME, "{}");
        $resultsArray = json_decode($resultsString, true);
        $resultsArray[$result->getTest()->getId()] = $result->getUuid();
        $this->requestStack->getSession()->set(self::SESSION_RESULT_NAME, json_encode($resultsArray));
    }

    public function clearSessionResult(Test $test)
    {
        $resultsString = $this->requestStack->getSession()->get(self::SESSION_RESULT_NAME, "{}");
        $resultsArray = json_decode($resultsString, true);
        $resultsArray[$test->getId()] = null;
        $this->requestStack->getSession()->set(self::SESSION_RESULT_NAME, json_encode($resultsArray));
    }

    public function getSessionResult(Test $test): ?Result
    {
        $resultsString = $this->requestStack->getSession()->get(self::SESSION_RESULT_NAME, "{}");
        $resultsArray = json_decode($resultsString, true);
        if (!empty($resultsArray[$test->getId()])) {
            $uuid = $resultsArray[$test->getId()];
            return $this->findByUuid($uuid);
        }
        return null;
    }

    public function create(Test $test, array $answers): Result
    {
        $result = Result::create($test, Uuid::v1()->toBase58(), $this->serializer->serialize($answers));
        return $this->save($result);
    }
}