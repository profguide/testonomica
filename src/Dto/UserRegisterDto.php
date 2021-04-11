<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserRegisterDto
{
    /**
     * @App\Validator\UniqueEmail
     */
    private string $email = "";

    private string $password = "";

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {

    }
}