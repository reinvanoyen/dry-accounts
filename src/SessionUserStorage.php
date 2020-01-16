<?php

namespace Tnt\Account;

use Oak\Session\Facade\Session;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Contracts\UserStorageInterface;

class SessionUserStorage implements UserStorageInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * SessionUserStorage constructor.
     * @param UserRepositoryInterface $userRepository,
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param AuthenticatableInterface $user
     * @return mixed|void
     */
    public function store(AuthenticatableInterface $user)
    {
        Session::set('user', $user->getIdentifier());
        Session::save();
    }

    /**
     * @return null|AuthenticatableInterface
     */
    public function retrieve(): ?AuthenticatableInterface
    {
        if ($this->isValid()) {

            $user = $this->userRepository->withIdentifier(Session::get('user'));

            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (!Session::has('user') || !Session::get('user')) {
            return false;
        }

        $user = $this->userRepository->withIdentifier(Session::get('user'));

        if (!$user) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed|void
     */
    public function clear()
    {
        Session::set('user', null);
        Session::save();
    }
}