<?php

namespace Tnt\Account;

use Oak\Contracts\Container\ContainerInterface;
use Oak\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
	public function boot(ContainerInterface $app)
	{
		//
	}

	public function register(ContainerInterface $app)
	{
		$app->singleton(Authentication::class, Authentication::class);
	}
}