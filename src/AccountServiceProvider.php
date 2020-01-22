<?php

namespace Tnt\Account;

use Oak\Contracts\Config\RepositoryInterface;
use Oak\Contracts\Container\ContainerInterface;
use Oak\Migration\MigrationManager;
use Oak\Migration\Migrator;
use Oak\ServiceProvider;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\UserFactoryInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\Account\Controller\AuthController;
use Tnt\Account\Model\User;
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
        $config = $app->get(RepositoryInterface::class);

        $model = $config->get('accounts.model', User::class);

        $app->set(UserStorageInterface::class, $config->get('accounts.storage', SessionUserStorage::class));

        $factory = $config->get('accounts.factory', UserFactory::class);
        $app->set(UserFactoryInterface::class, $factory);
        $app->whenAsksGive($factory, 'model', $model);

        $repository = $config->get('accounts.repository', UserRepository::class);
        $app->set(UserRepositoryInterface::class, $repository);
        $app->whenAsksGive($repository, 'model', $model);

        $app->set(AuthenticationInterface::class, Authentication::class);
    }
}