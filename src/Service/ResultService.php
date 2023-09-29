<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Service;


use App\Entity\Result;
use App\Entity\Test;
use App\Repository\ResultRepository;
use App\Test\Progress\Progress;
use App\Test\Progress\ProgressSerializer;
use Symfony\Component\HttpFoundation\RequestStack;

class ResultService
{
    private const SESSION_RESULT_NAME = 'results';

    public function __construct(
        private readonly RequestStack       $requestStack,
        private readonly ResultRepository   $repository,
        private readonly ProgressSerializer $progressSerializer)
    {
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

    /**
     * todo make Command
     * @param Test $test
     * @param Progress $progress
     * @return Result
     */
    public function create(Test $test, Progress $progress): Result
    {
        $result = Result::createAutoKey($test, $progress, $this->progressSerializer);
        return $this->save($result);
    }
}