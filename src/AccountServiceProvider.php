<?php

namespace Tnt\Account;

use Oak\Contracts\Container\ContainerInterface;
use Oak\ServiceProvider;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\Account\Controller\AuthController;
use Tnt\ExternalApi\Facade\Api;

class AccountServiceProvider extends ServiceProvider
{
    public function boot(ContainerInterface $app)
    {
        Api::get('1', 'authenticate', AuthController::class, 'authenticate');
        Api::get('1', 'authorize', AuthController::class, 'authorize');
    }

    public function register(ContainerInterface $app)
    {
        $app->set(UserStorageInterface::class, SessionUserStorage::class);
        $app->singleton(AuthenticationInterface::class, Authentication::class);
    }
}