<?php

namespace Tnt\Account\Events;

use Oak\Dispatcher\Event;
use Tnt\Account\Contracts\AuthenticatableInterface;

/**
 * Class UserEvent
 * @package Tnt\Account\Events
 */
abstract class UserEvent extends Event
{
    /**
     * @var AuthenticatableInterface $user
     */
    private $user;

    /**
     * Activated constructor.
     * @param AuthenticatableInterface $user
     */
    public function __construct(AuthenticatableInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return AuthenticatableInterface
     */
    public function getUser(): AuthenticatableInterface
    {
        return $this->user;
    }
}