<?php

namespace OpenCFP\Infrastructure\Auth;

use Cartalyst\Sentinel\Sentinel;
use OpenCFP\Domain\Services\AccountManagement;

class SentinelAccountManagement implements AccountManagement
{
    private $sentinel;

    public function __construct(Sentinel $sentinel)
    {
        $this->sentinel = $sentinel;
    }

    public function findById($userId): UserInterface
    {
        $user = $this->sentinel->getUserRepository()->findById($userId);

        return new SentinelUser($user);
    }

    public function findByLogin($email): UserInterface
    {
        $user = $this->sentinel->getUserRepository()->findByCredentials(['email' => $email]);

        return new SentinelUser($user);
    }

    public function findByRole($role)
    {
        return $this->sentinel->getRoleRepository()->findByName($role)->getUsers();
    }

    public function create($email, $password, array $data = []): UserInterface
    {
        $user = $this->sentinel
            ->getUserRepository()
            ->create(array_merge(['email' => $email, 'password' => $password], $data));

        return new SentinelUser($user);
    }

    public function activate($email)
    {
        $user           = $this->findByLogin($email)->getUser();
        $activationCode = $this->sentinel->getActivationRepository()->create($user)->getCode();

        return $this->sentinel->getActivationRepository()->complete($user, $activationCode);
    }

    public function promoteTo($email, $role = 'Admin')
    {
        $this->sentinel
            ->getRoleRepository()
            ->findByName(strtolower($role))
            ->users()
            ->attach($this->findByLogin($email)->getId());
    }

    public function demoteFrom($email, $role = 'Admin')
    {
        $this->sentinel
            ->getRoleRepository()
            ->findByName(strtolower($role))
            ->users()
            ->detach($this->findByLogin($email)->getId());
    }
}
