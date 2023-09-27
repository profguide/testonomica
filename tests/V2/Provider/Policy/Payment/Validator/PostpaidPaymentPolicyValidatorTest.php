<?php

declare(strict_types=1);

namespace App\Tests\V2\Provider\Policy\Payment\Validator;

use App\Entity\Provider;
use App\V2\Provider\Policy\Payment\Validator\PostpaidPaymentPolicyValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostpaidPaymentPolicyValidatorTest extends KernelTestCase
{
    private $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->validator = $container->get(PostpaidPaymentPolicyValidator::class);
    }

    public function testDoNothingYet(): void
    {
        $provider = new Provider();

        self::assertTrue($this->validator->validate($provider));
    }
}