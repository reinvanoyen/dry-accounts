<?php

namespace Tnt\Account;

use dry\db\FetchException;
use Oak\Dispatcher\Facade\Dispatcher;
use Oak\Session\Facade\Session;
use Tnt\Account\Contracts\AuthenticationInterface;
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
	 * @var User $user
	 */
	private $user;

	/**
	 * Authentication constructor.
	 */
	public function __construct()
	{
		$this->restore();
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @return bool
	 */
	public function authenticate(string $email, string $password): bool
	{
		if (! $this->user) {

			try {

				$this->user = User::one('
					WHERE email = ?
					AND MD5( CONCAT( ?, password_salt ) ) = password
					AND is_activated IS TRUE
				', $email, $password);

				Session::set('user', $this->user->id);
				Session::save();

				// Dispatch the Authenticated event
				Dispatcher::dispatch(Authenticated::class, new Authenticated($this->user));

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

			$user = $this->user;

			Session::set('user', null);
			Session::save();

			$this->user = null;

			// Dispatch the Logout event
			Dispatcher::dispatch(Logout::class, new Logout($user));
		}
	}

	/**
	 * @return bool
	 */
	public function isAuthenticated(): bool
	{
		return (bool) $this->user;
	}

	/**
	 * @return null|User
	 */
	public function getUser(): ?User
	{
		return $this->user;
	}

	/**
	 * Restores the session
	 */
	private function restore()
	{
		if (Session::has('user')) {
			try {
				$this->user = User::load(Session::get('user'));
			} catch (FetchException $e) {}
		}
	}
}