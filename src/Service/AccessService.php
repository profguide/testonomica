<?php
/**
 * @author: adavydov
 * @since: 13.11.2020
 */

namespace App\Service;


use App\Entity\Provider;
use App\Entity\ProviderAccess;
use App\Repository\AccessRepository;

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

    public function createWithProvider(Provider $provider): ?ProviderAccess
    {
        return $this->save(ProviderAccess::init($provider));
    }

    public function save(ProviderAccess $providerAccess)
    {
        return $this->repository->save($providerAccess);
    }

    public function findOneByToken(string $token): ?ProviderAccess
    {
        return $this->repository->findOneByToken($token);
    }
}