<?php

namespace Tnt\Account;

use dry\route\Router;
use Oak\Contracts\Container\ContainerInterface;
use Oak\Dispatcher\Event;
use Oak\Dispatcher\Facade\Dispatcher;
use Oak\ServiceProvider;
use Tnt\Account\Events\Activated;
use Tnt\Account\Events\Authenticated;
use Tnt\Account\Events\Created;
use Tnt\Account\Events\Logout;
use Tnt\Account\Facade\Auth;
use Tnt\Account\Model\User;

class AccountServiceProvider extends ServiceProvider
{
	public function boot(ContainerInterface $app)
	{
		$this->bindEvents();

		Router::register([
			'user-test/' => function($request) {

				if (Auth::isAuthenticated()) {

					$user = Auth::getUser();

					echo 'You are currently authenticated' . "\n";
					echo $user->email;

					return;
				}

				echo 'Not authenticated';
			},
			'user-create/' => function($request) {

				$user = new User();
				$user->email = 'rein@tnt.be';
				$user->password = 'test';
				$user->save();

				$user->activate();
			},
			'user-login/' => function($request) {

				if (Auth::authenticate('rein@tnt.be', 'test')) {
					echo 'Auth OK';
				} else {
					echo 'Auth NOT OK';
				}
			},
			'user-logout/' => function($request) {

				Auth::logout();
			}
		]);
	}

	public function register(ContainerInterface $app)
	{
		$app->singleton(Authentication::class, Authentication::class);
	}

	private function bindEvents()
	{
		Dispatcher::addListener(Authenticated::class, function(Event $event) {
			echo 'Logged in';
		});

		Dispatcher::addListener(Logout::class, function(Event $event) {
			echo 'Logged out';
		});

		Dispatcher::addListener(Created::class, function(Event $event) {
			echo 'Created';
		});

		Dispatcher::addListener(Activated::class, function(Event $event) {
			echo 'Activated';
		});
	}
}