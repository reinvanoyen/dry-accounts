<?php

namespace Tnt\Account;

use Oak\Contracts\Container\ContainerInterface;
use Oak\ServiceProvider;
use Tnt\Account\Contracts\AuthenticationInterface;

class AccountServiceProvider extends ServiceProvider
{
	public function boot(ContainerInterface $app)
	{
		//
	}

	public function register(ContainerInterface $app)
	{
		$app->singleton(AuthenticationInterface::class, Authentication::class);
	}
}