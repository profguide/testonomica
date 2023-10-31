<?php

declare(strict_types=1);

namespace App\Tests\V2\Result\Command;

use App\DataFixtures\ProviderFixture;
use App\DataFixtures\TestFixture;
use App\Entity\Answer;
use App\Entity\ProviderUser;
use App\Entity\Result;
use App\Repository\ProviderRepository;
use App\Repository\ProviderUsersResultsRepository;
use App\Repository\TestRepository;
use App\Test\Progress\Progress;
use App\Test\Progress\ProgressSerializer;
use App\V2\Result\Command\AttachResultToUser;
use App\V2\Result\Command\AttachResultToUserHandler;
use App\V2\Result\Exception\ResultAlreadyAttachedAnotherUserException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AttachResultToUserTest extends KernelTestCase
{
    private ?TestRepository $testRepository;
    private ?ProviderRepository $providerRepository;
    private ?ProviderUsersResultsRepository $userResultRepository;
    private ?EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->testRepository = $container->get(TestRepository::class);
        $this->providerRepository = $container->get(ProviderRepository::class);
        $this->userResultRepository = $container->get(ProviderUsersResultsRepository::class);

        $this->em = $container->get(EntityManagerInterface::class);
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Отменяем транзакцию после каждого теста
        $this->em->rollback();
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }

    /**
     * Проверяет, что создаётся запись в таблице связей пользователя и результата
     *
     * Условие
     * - запись отсутствует
     */
    public function testUserAttachSuccessWhenNoRecords()
    {
        $result = $this->createRandResult();
        $user = $this->createRandUser();

        $mockUserResultRepository = $this->createMock(ProviderUsersResultsRepository::class);
        $mockUserResultRepository
            ->method('hasByResultAndUser')
            ->willReturn(false); // << ещё нет связи результата с пользователем

        $handler = new AttachResultToUserHandler($this->em, $mockUserResultRepository);
        $handler(new AttachResultToUser($result, $user));

        self::assertTrue($this->userResultRepository->hasByResultAndUser($result, $user));
    }

    /**
     * Проверяет, что запись не создаётся если она уже создана
     *
     * Условие
     * - запись присутствует
     */
    public function testUserAttachDoNothingWhenAlreadyHasRecord()
    {
        $result = $this->createRandResult();
        $user = $this->createRandUser();

        $mockUserResultRepository = $this->createMock(ProviderUsersResultsRepository::class);
        $mockUserResultRepository
            ->method('hasByResultAndUser')
            ->willReturn(true); // << уже есть есть связь результата с пользователем

        $handler = new AttachResultToUserHandler($this->em, $mockUserResultRepository);
        $handler(new AttachResultToUser($result, $user));

        self::assertFalse($this->userResultRepository->hasByResultAndUser($result, $user));
    }

    /**
     * Проверяет, что выбрасывается исключение, когда происходит попытка привязать
     * пользователя к результату, который уже связан с другим пользователем
     *
     * Ожидаемый результат:
     * - создана запись в таблице связи
     */
    public function testThrowExceptionWhenAttachedAnotherUser()
    {
        $result = $this->createRandResult();
        $user = $this->createRandUser();

        $mockUserResultRepository = $this->createMock(ProviderUsersResultsRepository::class);
        $mockUserResultRepository
            ->method('hasByResultAndUser')
            ->willReturn(false); // << ещё нет связи результата с пользователем
        $mockUserResultRepository
            ->method('hasAnotherUserAttached')
            ->willReturn(true); // << уже есть связь результата с другим пользователем

        self::expectException(ResultAlreadyAttachedAnotherUserException::class);

        $handler = new AttachResultToUserHandler($this->em, $mockUserResultRepository);
        $handler(new AttachResultToUser($result, $user));
    }

    private function createRandResult(): Result
    {
        $test = $this->testRepository->findOneBy(['slug' => TestFixture::TEST_3_SLUG]);
        $result = Result::createAutoKey($test, new Progress([new Answer('1', ['a', 'b'])]), new ProgressSerializer());
        $this->em->persist($result);
        $this->em->flush();

        return $result;
    }

    private function createRandUser(): ProviderUser
    {
        $provider = $this->providerRepository->findOneBy(['slug' => ProviderFixture::PROFGUIDE]);
        $user = ProviderUser::create($provider, 'any');
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}