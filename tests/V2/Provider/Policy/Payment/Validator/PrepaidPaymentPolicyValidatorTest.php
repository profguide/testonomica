<?php

declare(strict_types=1);

namespace App\Tests\V2\Provider\Policy\Payment\Validator;

use App\Entity\Provider;
use App\V2\Provider\Policy\Payment\Validator\PrepaidPaymentPolicyValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PrepaidPaymentPolicyValidatorTest extends KernelTestCase
{
    private ?PrepaidPaymentPolicyValidator $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->validator = $container->get(PrepaidPaymentPolicyValidator::class);
    }

    public function testSuccessLimitNotReached(): void
    {
        $provider = new Provider();
        $provider->setAccessLimit(1000);
        $provider->setAccessCount(999);

        $result = $this->validator->validate($provider);

        self::assertTrue($result);
    }

    public function testFailLimitReached(): void
    {
        $provider = new Provider();
        $provider->setAccessLimit(1000);
        $provider->setAccessCount(1000);

        $result = $this->validator->validate($provider);

        self::assertFalse($result);
        self::assertEquals(PrepaidPaymentPolicyValidator::ERROR_MESSAGE_LIMIT_REACHED, $this->validator->getMessage());
    }
}