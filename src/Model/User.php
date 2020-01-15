<?php

namespace Tnt\Account\Model;

use dry\orm\Model;
use dry\orm\special\Boolean;
use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Events\Activated;
use Tnt\Account\Events\Created;

class User extends Model implements AuthenticatableInterface
{
    protected static $authIdentifierName = 'email';
    protected static $tokenName = 'temp_token';

    const TABLE = 'user';

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

            $this->setPassword();
            $this->temp_token = \dry\util\string\random(10);
            parent::save();

            Dispatcher::dispatch(Created::class, new Created($this));
            return;
        }

        $this->updated = time();
        parent::save();
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
     *
     */
    private function setPassword()
    {
        $this->password_salt = \dry\util\string\random(10);
        $this->password = md5($this->password.$this->password_salt);
    }
}