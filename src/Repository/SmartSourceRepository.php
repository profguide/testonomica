<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Test;
use App\Test\TestSourceRepositoryFactory;

/**
 * @author: adavydov
 * @since: 23.10.2020
 * Такие планы по кешированию, если окажется, что медленно работает
 * 1 вынести логику работы с XML в отдельный класс SourceXmlRepository
 * 2 добавить SourceCacheRepositoryInterface <- SourceRedisRepository
 * 3 здесь сделать работу с обоими репозиториями, используя такие методы:
 * $this->xmlRepository->load($test)
 * $this->redisRepository->isTestPersist($test)
 * $this->redisRepository->saveAll(Question[])
 */
class SmartSourceRepository implements SourceRepositoryInterface
{
    private TestSourceRepositoryFactory $repositoryFactory;

    public function __construct(TestSourceRepositoryFactory $repositoryFactory)
    {
        $this->repositoryFactory = $repositoryFactory;
    }

    function getQuestion(Test $test, $id): Question
    {
        return $this->getRepository($test)->getQuestion($test, $id);
    }

    function getNextQuestion(Test $test, $id): ?Question
    {
        return $this->getRepository($test)->getNextQuestion($test, $id);
    }

    function getPrevQuestion(Test $test, $id): ?Question
    {
        return $this->getRepository($test)->getPrevQuestion($test, $id);
    }

    function getFirstQuestion(Test $test): Question
    {
        return $this->getRepository($test)->getFirstQuestion($test);
    }

    function getLastQuestion(Test $test): Question
    {
        return $this->getRepository($test)->getLastQuestion($test);
    }

    function getAllQuestions(Test $test): array
    {
        return $this->getRepository($test)->getAllQuestions($test);
    }

    function getTotalCount(Test $test): int
    {
        return $this->getRepository($test)->getTotalCount($test);
    }

    function getQuestionNumber(Test $test, $id): int
    {
        return $this->getRepository($test)->getQuestionNumber($test, $id);
    }

    public function getInstruction(Test $test): ?string
    {
        return $this->getRepository($test)->getInstruction($test);
    }

    private function getRepository(Test $test): SourceRepositoryInterface
    {
        return $this->repositoryFactory->createSource($test);
    }
}