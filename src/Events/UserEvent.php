<?php

namespace Tnt\Account\Events;

use Oak\Dispatcher\Event;
use Tnt\Account\Model\User;

/**
 * Class UserEvent
 * @package Tnt\Account\Events
 */
abstract class UserEvent extends Event
{
	/**
	 * @var User $user
	 */
	private $user;

	/**
	 * Activated constructor.
	 * @param User $user
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}
}