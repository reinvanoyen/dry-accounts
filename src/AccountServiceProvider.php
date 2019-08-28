<?php

namespace Tnt\Account;

use Oak\Contracts\Container\ContainerInterface;
use Oak\ServiceProvider;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\ExternalApi\Facade\Api;

class AccountServiceProvider extends ServiceProvider
{
	public function boot(ContainerInterface $app)
	{
		Api::get('1', 'authenticate/', 'app\\auth\\controller\\auth::authenticate');
		Api::get('1', 'authorize/', 'app\\auth\\controller\\auth::authorize');
	}

	public function register(ContainerInterface $app)
	{
		$app->set(UserStorageInterface::class, SessionUserStorage::class);
		$app->singleton(AuthenticationInterface::class, Authentication::class);
	}
}