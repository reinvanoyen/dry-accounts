<?php

namespace Tnt\Account\Contracts;

interface RegisterableInterface
{
    public static function register(string $identifier, string $password): ?AuthenticatableInterface;
}