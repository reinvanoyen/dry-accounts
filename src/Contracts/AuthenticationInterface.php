<?php

namespace Tnt\Account\Contracts;

Interface AuthenticationInterface
{
    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|AuthenticatableInterface
     */
    public function register(string $authIdentifier, string $password): ?AuthenticatableInterface;

    /**
     * @param string $authIdentifier
     * @param string $password
     * @return bool
     */
    public function authenticate(string $authIdentifier, string $password): bool;

    /**
     * @return mixed
     */
    public function logout();

    /**
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * @return null|AuthenticatableInterface
     */
    public function getUser(): ?AuthenticatableInterface;

    /**
     * @param string $authIdentifier
     * @return null|AuthenticatableInterface
     */
    public function getActivatedUser(string $authIdentifier): ?AuthenticatableInterface;
}