<?php
/**
 * @author: adavydov
 * @since: 13.11.2020
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Access;
use App\Entity\Service;
use App\Repository\AccessRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessService
{
    private AccessRepository $repository;

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

    public function save(Access $providerAccess): Access
    {
        return $this->repository->save($providerAccess);
    }

    public function findOneByToken(string $token): ?Access
    {
        return $this->repository->findOneByToken($token);
    }

    public function utilize(Access $access)
    {
        $access->setUsed();
        $this->repository->save($access);
    }

    // todo AccessCookie::setCookie($access, $response);
    public function setCookie(Access $access, Response $response)
    {
        $cookie = Cookie::create('access', $access->getToken(), time() + 60 * 60 * 24 * 365);
        $response->headers->setCookie($cookie);
    }

    public function getCookie(Request $request): ?string
    {
        return $request->cookies->get('access');
    }
}