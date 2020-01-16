<?php

namespace Tnt\Account\Contracts;

interface AuthenticatableInterface
{
    /**
     * @return mixed
     */
    public function getIdentifier(): int;

    /**
     * @return string
     */
    public function getAuthIdentifier(): ?string;

    /**
     * @return string
     */
    public function getPassword(): string;
}