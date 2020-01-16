<?php

namespace Tnt\Account\Contracts;

interface UserStorageInterface
{
    /**
     * @param AuthenticatableInterface $user
     * @return mixed
     */
    public function store(AuthenticatableInterface $user);

    /**
     * @return null|AuthenticatableInterface
     */
    public function retrieve(): ?AuthenticatableInterface;

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return mixed
     */
    public function clear();
}