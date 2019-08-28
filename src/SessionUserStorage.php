<?php

namespace Tnt\Account;

use dry\db\FetchException;
use Oak\Session\Facade\Session;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\Account\Model\User;

class SessionUserStorage implements UserStorageInterface
{
	/**
	 * @param User $user
	 * @return mixed|void
	 */
	public function store(User $user)
	{
		Session::set('user', $user->id);
		Session::save();
	}

	/**
	 * @return null|User
	 */
	public function retrieve(): ?User
	{
		if ($this->isValid()) {

			try {
				return User::load(Session::get('user'));
			} catch (FetchException $e) {
				//
			}

			return null;
		}
	}

	/**
	 * @return bool
	 */
	public function isValid(): bool
	{
		if (!Session::has('user') || !Session::get('user')) {
			return false;
		}

		try {
			User::load(Session::get('user'));
			return true;
		} catch (FetchException $e) {
			//
		}

		return false;
	}

	/**
	 * @return mixed|void
	 */
	public function clear()
	{
		Session::set('user', null);
		Session::save();
	}
}