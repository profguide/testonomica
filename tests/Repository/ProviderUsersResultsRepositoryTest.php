<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\DataFixtures\ProviderFixture;
use App\DataFixtures\TestFixture;
use App\Entity\Answer;
use App\Entity\ProviderUser;
use App\Entity\ProviderUserResult;
use App\Entity\Result;
use App\Repository\ProviderRepository;
use App\Repository\ProviderUsersResultsRepository;
use App\Repository\TestRepository;
use App\Test\Progress\Progress;
use App\Test\Progress\ProgressSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

final class ProviderUsersResultsRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ProviderUsersResultsRepository $repository;
    private TestRepository $testRepository;
    private ProviderRepository $providerRepository;

    public function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->repository = static::getContainer()->get(ProviderUsersResultsRepository::class);
        $this->testRepository = static::getContainer()->get(TestRepository::class);
        $this->providerRepository = static::getContainer()->get(ProviderRepository::class);
    }

    /**
     * Проверяет, что запись находится.
     *
     * Условия:
     * - запись существует
     *
     * Ожидаемый результат:
     * - функция возвращает true
     */
    public function testHasByResultAndUser()
    {
        // arrange
        $test = $this->testRepository->findOneBy(['slug' => TestFixture::TEST_3_SLUG]);

        $result = Result::createAutoKey($test, new Progress([new Answer('1', ['a', 'b'])]), new ProgressSerializer());
        $this->em->persist($result);
        $this->em->flush();

        $provider = $this->providerRepository->findOneBy(['slug' => ProviderFixture::PROFGUIDE]);
        $user = ProviderUser::create($provider, 'any');
        $this->em->persist($user);
        $this->em->flush();

        $userResult = ProviderUserResult::create($user, $result, $test);
        $this->em->persist($userResult);
        $this->em->flush();

        // assert
        self::assertTrue($this->repository->hasByResultAndUser($result, $user));
    }

    /**
     * Проверяет, что запись находится
     *
     * Условия:
     * - существует результат с некоторым пользователем
     * - функция принимает иного пользователя
     *
     * Ожидаемый результат:
     * - функция возвращает false
     */
    public function testHasAnotherUserAttached()
    {
        // arrange
        $test = $this->testRepository->findOneBy(['slug' => TestFixture::TEST_3_SLUG]);

        $result = Result::createAutoKey($test, new Progress([new Answer('1', ['a', 'b'])]), new ProgressSerializer());
        $this->em->persist($result);
        $this->em->flush();

        $provider = $this->providerRepository->findOneBy(['slug' => ProviderFixture::PROFGUIDE]);
        $user1 = ProviderUser::create($provider, 'any');
        $this->em->persist($user1);
        $this->em->flush();

        $userResult = ProviderUserResult::create($user1, $result, $test);
        $this->em->persist($userResult);
        $this->em->flush();

        $user2 = ProviderUser::create($provider, 'any');
        $user2->setId(UuidV4::v4());

        // assert
        self::assertFalse($this->repository->hasAnotherUserAttached($result, $user1));
        self::assertTrue($this->repository->hasAnotherUserAttached($result, $user2));
    }
}