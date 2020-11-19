<?php
/**
 * @author: adavydov
 * @since: 13.11.2020
 */

namespace App\Service;


use App\Entity\Access;
use App\Entity\Service;
use App\Repository\AccessRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AccessService
{
    /**@var AccessRepository */
    private $repository;

    /**
     * AccessService constructor.
     * @param AccessRepository $repository
     */
    public function __construct(AccessRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(Service $service): ?Access
    {
        return $this->save(Access::init($service));
    }

    public function save(Access $providerAccess)
    {
        return $this->repository->save($providerAccess);
    }

    public function findOneByToken(string $token): ?Access
    {
        return $this->repository->findOneByToken($token);
    }

    public function saveToCookie(Access $access, Response $response)
    {
        $response->headers->setCookie(Cookie::create('access', $access->getId(), 60 * 60 * 24 * 365));
    }
}