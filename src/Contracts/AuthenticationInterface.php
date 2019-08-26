<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Model\User;

Interface AuthenticationInterface
{
	/**
	 * @param string $email
	 * @param string $password
	 * @return bool
	 */
	public function authenticate(string $email, string $password): bool;

	/**
	 * @return mixed
	 */
	public function logout();

	/**
	 * @return bool
	 */
	public function isAuthenticated(): bool;

	/**
	 * @return null|User
	 */
	public function getUser(): ?User;
}