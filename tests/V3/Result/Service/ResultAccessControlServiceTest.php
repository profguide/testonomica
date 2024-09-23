<?php

declare(strict_types=1);

namespace App\Tests\V3\Result\Service;

use App\Entity\Result;
use App\Entity\Test;
use App\Repository\ProviderUsersResultsRepository;
use App\V3\Result\Exception\ResultUserAssociationMissingException;
use App\V3\Result\Service\ResultAccessControlService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ResultAccessControlServiceTest extends KernelTestCase
{
    private ProviderUsersResultsRepository $usersResultsRepository;
    private ResultAccessControlService $service;

    protected function setUp(): void
    {
        // Создаем мок репозитория
        $this->usersResultsRepository = $this->createMock(ProviderUsersResultsRepository::class);
        // Создаем сервис
        $this->service = new ResultAccessControlService($this->usersResultsRepository);
    }


    public function testFreeTestDoesNotThrowException(): void
    {
        // Создаем тест, который бесплатный
        $test = $this->createMock(Test::class);
        $test->method('isFree')->willReturn(true);

        // Создаем результат с этим тестом
        $result = $this->createMock(Result::class);
        $result->method('getTest')->willReturn($test);

        // Проверка, что исключение не выбрасывается
        $this->service->guardResultAccess($result);

        $this->addToAssertionCount(1); // Добавляем к числу проверок
    }

    public function testPaidTestWithAssociatedUserDoesNotThrowException(): void
    {
        // Создаем тест, который платный
        $test = $this->createMock(Test::class);
        $test->method('isFree')->willReturn(false);

        // Создаем результат с этим тестом
        $result = $this->createMock(Result::class);
        $result->method('getTest')->willReturn($test);

        // Настраиваем репозиторий, чтобы возвращал true, т.е. пользователь проассоциирован
        $this->usersResultsRepository
            ->method('hasRecordByResult')
            ->with($result)
            ->willReturn(true);

        // Проверка, что исключение не выбрасывается
        $this->service->guardResultAccess($result);

        $this->addToAssertionCount(1); // Добавляем к числу проверок
    }

    public function testPaidTestWithNonAssociatedUserThrowsException(): void
    {
        // Создаем тест, который платный
        $test = $this->createMock(Test::class);
        $test->method('isFree')->willReturn(false);

        // Создаем результат с этим тестом
        $result = $this->createMock(Result::class);
        $result->method('getTest')->willReturn($test);

        // Настраиваем репозиторий, чтобы возвращал false, т.е. пользователь не проассоциирован
        $this->usersResultsRepository
            ->method('hasRecordByResult')
            ->with($result)
            ->willReturn(false);

        // Проверка, что исключение выбрасывается
        $this->expectException(ResultUserAssociationMissingException::class);

        $this->service->guardResultAccess($result);
    }
}