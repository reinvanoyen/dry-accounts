<?php

namespace Tnt\Account;

use Oak\Contracts\Config\RepositoryInterface;
use Oak\Contracts\Container\ContainerInterface;
use Oak\Migration\MigrationManager;
use Oak\Migration\Migrator;
use Oak\ServiceProvider;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\Account\Controller\AuthController;
use Tnt\Account\Revisions\CreateUserTable;
use Tnt\ExternalApi\Facade\Api;

class AccountServiceProvider extends ServiceProvider
{
    public function boot(ContainerInterface $app)
    {
        Api::get('1', 'authenticate', AuthController::class, 'authenticate');
        Api::get('1', 'authorize', AuthController::class, 'authorize');

        if ($app->isRunningInConsole()) {

            $migrator = $app->getWith(Migrator::class, [
                'name' => 'account'
            ]);

            $migrator->setRevisions([
                CreateUserTable::class
            ]);

            $app->get(MigrationManager::class)->addMigrator($migrator);
        }
    }

    public function register(ContainerInterface $app)
    {
        $app->set(UserRepositoryInterface::class, UserRepository::class);
        $app->set(UserStorageInterface::class, $app->get(RepositoryInterface::class)->get('accounts.storage', SessionUserStorage::class));
        $app->singleton(AuthenticationInterface::class, Authentication::class);
    }
}