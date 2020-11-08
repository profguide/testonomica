<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Service;


use App\Entity\Result;
use App\Entity\Test;
use App\Repository\ResultRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ResultService
{
    private const SESSION_RESULT_NAME = 'results';

    /**@var ResultRepository */
    private $repository;

    /**@var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session, ResultRepository $repository)
    {
        $this->repository = $repository;
        $this->session = $session;
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
        $resultsString = $this->session->get(self::SESSION_RESULT_NAME, "{}");
        $resultsArray = json_decode($resultsString, true);
        $resultsArray[$result->getTest()->getId()] = $result->getUuid();
        $this->session->set(self::SESSION_RESULT_NAME, json_encode($resultsArray));
    }

    public function clearSessionResult(Test $test)
    {
        $resultsString = $this->session->get(self::SESSION_RESULT_NAME, "{}");
        $resultsArray = json_decode($resultsString, true);
        $resultsArray[$test->getId()] = null;
        $this->session->set(self::SESSION_RESULT_NAME, json_encode($resultsArray));
    }

    public function getSessionResult(Test $test): ?Result
    {
        $resultsString = $this->session->get(self::SESSION_RESULT_NAME, "{}");
        $resultsArray = json_decode($resultsString, true);
        if (!empty($resultsArray[$test->getId()])) {
            $uuid = $resultsArray[$test->getId()];
            return $this->findByUuid($uuid);
        }
        return null;
    }
}