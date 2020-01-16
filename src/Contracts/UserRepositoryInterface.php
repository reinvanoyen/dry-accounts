<?php

namespace Tnt\Account\Contracts;

interface UserRepositoryInterface
{
    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|AuthenticatableInterface
     */
    public function withCredentials(string $authIdentifier, string $password): ?AuthenticatableInterface;

    /**
     * @param string $authIdentifier
     * @return null|AuthenticatableInterface
     */
    public function withAuthIdentifier(string $authIdentifier): ?AuthenticatableInterface;

    /**
     * @param int $id
     * @return null|AuthenticatableInterface
     */
    public function withIdentifier(int $id): ?AuthenticatableInterface;

    /**
     * @param string $authIdentifier
     * @return null|AuthenticatableInterface
     */
    public function getActivated(string $authIdentifier): ?AuthenticatableInterface;
}