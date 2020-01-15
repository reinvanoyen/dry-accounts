<?php

namespace Tnt\Account;

use dry\db\FetchException;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Model\User;
use Tnt\Dbi\BaseRepository;
use Tnt\Dbi\Contracts\RepositoryInterface;
use Tnt\Dbi\Criteria\Equals;
use Tnt\Dbi\Raw;

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
            $this->addCriteria(new Equals(new Raw('MD5( CONCAT( ?, password_salt ) )', [$password]), new Raw('password')));

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