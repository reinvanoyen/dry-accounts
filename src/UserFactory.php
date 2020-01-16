<?php

namespace Tnt\Account;

use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\RegisterableInterface;
use Tnt\Account\Contracts\UserFactoryInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;

class UserFactory implements UserFactoryInterface
{
    /**
     * @var string
     */
    private $model;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * UserFactory constructor.
     * @param string $model
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(string $model, UserRepositoryInterface $userRepository)
    {
        $this->model = $model;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|AuthenticatableInterface
     * @throws \Exception
     */
    public function register(string $authIdentifier, string $password): ?AuthenticatableInterface
    {
        $user = $this->userRepository->withAuthIdentifier($authIdentifier);

        if (! $user) {

            if (! in_array(RegisterableInterface::class, class_implements($this->model))) {

                throw new \Exception('Authentication model is not of type RegisterableInterface');
            }

            return ($this->model)::register($authIdentifier, $password);
        }

        $user->setPassword($password);
        $user->save();

        return $user;
    }
}