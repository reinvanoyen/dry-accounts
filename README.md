# DRY Accounts
Account system for DRY applications

#### Installation
```ssh
composer require reinvanoyen/dry-accounts
```

#### Basic example usage

##### Define authenticate service provider
```php
<?php

namespace Auth;

use Repository\UserRepository;
use Oak\Contracts\Container\ContainerInterface;
use Oak\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	public function boot(ContainerInterface $app)
	{
		//
	}

	public function register(ContainerInterface $app)
	{
		$app->set(UserRepositoryInterface::class, UserRepository::class);
	}
}
```

##### User repository definition
```php
<?php

namespace app\api\user\Repository;

use app\api\user\Model\User;
use dry\db\FetchException;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Dbi\BaseRepository;
use Tnt\Dbi\Contracts\RepositoryInterface;
use Tnt\Dbi\Criteria\Equals;

class UserRepository extends BaseRepository implements UserRepositoryInterface, RepositoryInterface
{
	protected $model = User::class;

	/**
	 * @param string $authIdentifier
	 * @param string $password
	 * @return User
	 */
	public function withCredentials(string $authIdentifier, string $password): ?AuthenticatableInterface
	{
		try
		{
			$this->addCriteria(new Equals('email', $authIdentifier));
			$this->addCriteria(new Equals('password', $password));

			return $this->first();
		}
		catch ( FetchException $e )
		{
			return null;
		}
	}

	/**
	 * @param int $id
	 * @return null|AuthenticatableInterface
	 */
	public function withIdentifier(int $id): ?AuthenticatableInterface
	{
		try
		{
			$this->addCriteria(new Equals('id', $id));

			return $this->first();
		}
		catch ( FetchException $e )
		{
			return null;
		}
	}
}
```

##### Usage
```php
<?php

namespace controller;

use Tnt\Account\Facade\Auth;

class authentication
{
    public static function logout()
    {
        Auth::logout();
    }
}
```