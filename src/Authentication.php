<?php

namespace Tnt\Account;

use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\Account\Events\Authenticated;
use Tnt\Account\Events\Logout;

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
	 * @var UserRepositoryInterface
	 */
	private $userRepository;

	/**
	 * Authentication constructor.
	 * @param UserStorageInterface $userStorage
	 * @param UserRepositoryInterface $userRepository
	 */
	public function __construct(UserStorageInterface $userStorage, UserRepositoryInterface $userRepository)
	{
		$this->userStorage = $userStorage;
		$this->userRepository = $userRepository;
	}

	/**
	 * @param string $authIdentifier
	 * @param string $password
	 * @return bool
	 */
	public function authenticate(string $authIdentifier, string $password): bool
	{
		if (! $this->userStorage->isValid()) {

			$user = $this->userRepository->withCredentials($authIdentifier, $password);

			if ($user) {

				// Dispatch the Authenticated event
				Dispatcher::dispatch(Authenticated::class, new Authenticated($user));

				// Store the user
				$this->userStorage->store($user);

				return true;
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
	 * @return null|AuthenticatableInterface
	 */
	public function getUser(): ?AuthenticatableInterface
	{
		return $this->userStorage->retrieve();
	}
}