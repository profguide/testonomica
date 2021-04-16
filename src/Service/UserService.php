<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(UserInterface $user)
    {
        $this->repository->save($user);
    }
}