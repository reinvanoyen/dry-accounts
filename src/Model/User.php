<?php

namespace Tnt\Account\Model;

use dry\orm\Model;
use dry\orm\special\Boolean;
use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\RegisterableInterface;
use Tnt\Account\Events\Activated;
use Tnt\Account\Events\Created;

class User extends Model implements AuthenticatableInterface, RegisterableInterface
{
    protected static $authIdentifierName = 'email';
    protected static $tokenName = 'temp_token';

    const TABLE = 'account_user';

    public static $special_fields = [
        'is_activated' => Boolean::class,
    ];

    public function activate()
    {
        $this->is_activated = true;
        $this->temp_token = null;
        $this->save();

        Dispatcher::dispatch(Activated::class, new Activated($this));
    }

    public function save()
    {
        if (! $this->id) {

            $this->created = time();
            $this->updated = time();

            $this->setPassword($this->password);
            $this->temp_token = \dry\util\string\random(10);
            parent::save();

            Dispatcher::dispatch(Created::class, new Created($this));
            return;
        }

        $this->updated = time();
        parent::save();
    }

    /**
     * implements RegisterableInterface
     */

    /**
     * @param string $identifier
     * @param string $password
     * @return null|AuthenticatableInterface
     */
    public static function register(string $identifier, string $password): ?AuthenticatableInterface
    {
        $user = new User();
        $user->email = $identifier;
        $user->password = $password;
        $user->save();

        return $user;
    }

    /**
     * implements AuthenticatableInterface
     */

    /**
     * @return mixed
     */
    public function getIdentifier(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public static function getAuthIdentifierName(): string
    {
        return static::$authIdentifierName;
    }

    /**
     * @return string
     */
    public function getAuthIdentifier(): ?string
    {
        if (!isset($this->{$this->getAuthIdentifierName()})) {
            return null;
        }

        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password_salt = \dry\util\string\random(10);
        $this->password = md5($password.$this->password_salt);
    }
}