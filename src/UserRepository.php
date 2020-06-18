<?php

namespace Tnt\Account;

use dry\db\FetchException;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Facade\Auth;
use Tnt\Account\Model\User;
use Tnt\Dbi\BaseRepository;
use Tnt\Dbi\Contracts\CriteriaCollectionInterface;
use Tnt\Dbi\Contracts\RepositoryInterface;
use Tnt\Dbi\Criteria\Equals;
use Tnt\Dbi\Criteria\GreaterThan;
use Tnt\Dbi\Criteria\IsTrue;
use Tnt\Dbi\Raw;

class UserRepository extends BaseRepository implements UserRepositoryInterface, RepositoryInterface
{
    /**
     * UserRepository constructor.
     * @param string $model
     * @param CriteriaCollectionInterface $criteria
     */
    public function __construct(string $model, CriteriaCollectionInterface $criteria)
    {
        $this->model = $model;

        parent::__construct($criteria);
    }

    /**
     * @param string $authIdentifier
     * @param string $password
     * @return User
     */
    public function withCredentials(string $authIdentifier, string $password): ?AuthenticatableInterface
    {
        try {
            $this->addCriteria(new Equals(($this->model)::getAuthIdentifierName(), $authIdentifier));
            $this->addCriteria(new Equals(new Raw('MD5( CONCAT( ?, password_salt ) )', [$password]), new Raw('password')));

            return $this->first();

        } catch ( FetchException $e ) {
            return null;
        }
    }

    /**
     * @param string $authIdentifier
     * @return null|AuthenticatableInterface
     */
    public function withAuthIdentifier(string $authIdentifier): ?AuthenticatableInterface
    {
        try {
            $this->addCriteria(new Equals(($this->model)::getAuthIdentifierName(), $authIdentifier));

            return $this->first();

        } catch ( FetchException $e ) {
            return null;
        }
    }

    /**
     * @param int $id
     * @return null|AuthenticatableInterface
     */
    public function withIdentifier(int $id): ?AuthenticatableInterface
    {
        try {
            $this->addCriteria(new Equals('id', $id));

            return $this->first();

        } catch ( FetchException $e ) {
            return null;
        }
    }

    /**
     * @param string $refreshToken
     * @return null|AuthenticatableInterface
     */
    public function withValidRefreshToken(string $refreshToken): ?AuthenticatableInterface
    {
        try {
            $this->addCriteria(new Equals('refresh_token', $refreshToken));
            $this->addCriteria(new GreaterThan('refresh_token_expiry_time', time()));

            return $this->first();

        } catch ( FetchException $e ) {
            return null;
        }
    }

    /**
     * @param string $authIdentifier
     * @return null|AuthenticatableInterface
     */
    public function getActivated(string $authIdentifier): ?AuthenticatableInterface
    {
        try {
            $this->addCriteria(new Equals(($this->model)::getAuthIdentifierName(), $authIdentifier));
            $this->addCriteria(new IsTrue('is_activated'));

            return $this->first();

        } catch ( FetchException $e ) {
            return null;
        }
    }
}