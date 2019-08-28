<?php

namespace Tnt\Account;

use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\Account\Events\Authenticated;
use Tnt\Account\Events\Logout;
use Tnt\Account\Model\User;

/**
 * Class Authentication
 * @package Tnt\Account
 */
class Authentication implements AuthenticationInterface
{
	/**
	 * @var UserStorageInterface $userStorage
	 */
	private $userStorage;

	/**
	 * Authentication constructor.
	 * @param UserStorageInterface $userStorage
	 */
	public function __construct(UserStorageInterface $userStorage)
	{
		$this->userStorage = $userStorage;
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @return bool
	 */
	public function authenticate(string $email, string $password): bool
	{
		if (! $this->userStorage->isValid()) {

			try {

				$user = User::one('
					WHERE email = ?
					AND MD5( CONCAT( ?, password_salt ) ) = password
					AND is_activated IS TRUE
				', $email, $password);

				// Dispatch the Authenticated event
				Dispatcher::dispatch(Authenticated::class, new Authenticated($user));

				// Store the user
				$this->userStorage->store($user);

				return true;

			} catch (\Exception $e) {
			}
		}

		return false;
	}

	/**
	 *
	 */
	public function logout()
	{
		if ($this->isAuthenticated()) {

			$user = $this->userStorage->retrieve();
			$this->userStorage->clear();

			// Dispatch the Logout event
			Dispatcher::dispatch(Logout::class, new Logout($user));
		}
	}

	/**
	 * @return bool
	 */
	public function isAuthenticated(): bool
	{
		return (bool) $this->userStorage->isValid();
	}

	/**
	 * @return null|User
	 */
	public function getUser(): ?User
	{
		return $this->userStorage->retrieve();
	}
}