<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Model\User;

interface UserStorageInterface
{
	/**
	 * @param User $user
	 * @return mixed
	 */
	public function store(User $user);

	/**
	 * @return null|User
	 */
	public function retrieve(): ?User;

	/**
	 * @return bool
	 */
	public function isValid(): bool;

	/**
	 * @return mixed
	 */
	public function clear();
}