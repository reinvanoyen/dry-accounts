<?php

namespace Tnt\Account\Contracts;

interface UserFactoryInterface
{
    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|AuthenticatableInterface
     */
    public function register(string $authIdentifier, string $password): ?AuthenticatableInterface;
}