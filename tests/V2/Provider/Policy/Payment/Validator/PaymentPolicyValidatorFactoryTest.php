<?php

declare(strict_types=1);

namespace App\Tests\V2\Provider\Policy\Payment\Validator;

use App\V2\Provider\Policy\Payment\PaymentPolicy;
use App\V2\Provider\Policy\Payment\Validator\PaymentPolicyValidatorFactory;
use App\V2\Provider\Policy\Payment\Validator\PostpaidPaymentPolicyValidator;
use App\V2\Provider\Policy\Payment\Validator\PrepaidPaymentPolicyValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PaymentPolicyValidatorFactoryTest extends KernelTestCase
{
    private ?PaymentPolicyValidatorFactory $factory;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->factory = $container->get(PaymentPolicyValidatorFactory::class);
    }

    public function testCreatePrepaidValidator(): void
    {
        $validator = $this->factory->createValidator(PaymentPolicy::PRE);
        self::assertEquals(PrepaidPaymentPolicyValidator::class, get_class($validator));
    }

    public function testCreatePostpaidValidator(): void
    {
        $validator = $this->factory->createValidator(PaymentPolicy::POST);
        self::assertEquals(PostpaidPaymentPolicyValidator::class, get_class($validator));
    }
}