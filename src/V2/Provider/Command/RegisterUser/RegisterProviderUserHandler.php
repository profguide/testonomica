<?php

declare(strict_types=1);

namespace App\V2\Provider\Command\RegisterUser;

use App\Entity\ProviderUser;
use App\Repository\ProviderUserRepository;
use App\V2\Provider\Policy\Payment\Validator\Exception\PaymentPolicyValidationException;
use App\V2\Provider\Policy\Payment\Validator\PaymentPolicyValidatorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @see RegisterProviderUser
 * @see RegisterProviderUserTest
 *
 * Создаёт пользователя провайдера.
 * Перед этим валидирует условия создания пользователя.
 */
#[AsMessageHandler]
class RegisterProviderUserHandler
{
    public function __construct(
        private ProviderUserRepository        $repository,
        private EntityManagerInterface        $entityManager,
        private PaymentPolicyValidatorFactory $paymentPolicyValidatorFactory,
    )
    {
    }

    /**
     * @param RegisterProviderUser $message
     * @return ProviderUser
     * @throws PaymentPolicyValidationException
     */
    public function __invoke(RegisterProviderUser $message): ProviderUser
    {
        $provider = $message->provider;
        $userId = $message->userId;

        $user = $this->repository->findOneByProviderAndExtUserId($provider, $userId);
        if ($user) {
            return $user;
        }

        $paymentPolicyValidator = $this->paymentPolicyValidatorFactory->createValidator($provider->getPaymentPolicy());
        if (!$paymentPolicyValidator->validate($provider)) {
            throw new PaymentPolicyValidationException($paymentPolicyValidator->getMessage());
        }

        $user = ProviderUser::create($provider, $userId);
        $provider->addUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($provider);
        $this->entityManager->flush();

        return $user;
    }
}