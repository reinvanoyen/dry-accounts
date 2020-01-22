<?php

namespace Tnt\Account\Controller;

use Oak\Contracts\Config\RepositoryInterface;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\ExternalApi\Exception\ApiException;
use Tnt\ExternalApi\Http\Request;

class AuthController
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var RepositoryInterface
     */
    private $config;

    /**,
     * @var string secret
     */
    private $secret;

    /**
     * AuthController constructor.
     * @param UserRepositoryInterface $userRepository
     * @param AuthenticationInterface $authentication
     * @param RepositoryInterface $config
     */
    public function __construct(UserRepositoryInterface $userRepository, AuthenticationInterface $authentication, RepositoryInterface $config)
    {
        $this->userRepository = $userRepository;
        $this->authentication = $authentication;
        $this->config = $config;

        $this->secret = $config->get('accounts.jwt_secret', '');
    }

    /**
     * @param Request $request
     * @return string
     * @throws ApiException
     */
    public function authenticate(Request $request)
    {
        if (! $request->getHeader('USER') || ! $request->getHeader('PASSWORD')) {
            throw new ApiException('auth_failed');
        }

        if (! $this->authentication->authenticate($request->getHeader('USER'), $request->getHeader('PASSWORD'))) {
            throw new ApiException('auth_failed');
        }

        $user = $this->authentication->getUser();

        try {

            $jwt = new \Lindelius\JWT\StandardJWT();
            $jwt->exp = $this->config->get('accounts.token_expiry_time', time() + (60 * 60));
            $jwt->iat = time();
            $jwt->sub = $user->getIdentifier();

            return $jwt->encode($this->secret);

        }
        catch (\Lindelius\JWT\Exception\Exception $e) {
            throw new ApiException('invalid_jwt', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return null|\Tnt\Account\Contracts\AuthenticatableInterface
     * @throws ApiException
     */
    public function authorize(Request $request)
    {
        if (! $request->getHeader('AUTHORIZATION')) {
            throw new ApiException('authorize_failed');
        }

        try {

            $decodedJwt = \Lindelius\JWT\StandardJWT::decode($request->getHeader('AUTHORIZATION'));
            $decodedJwt->verify($this->secret);

            $user = $this->userRepository->withIdentifier($decodedJwt->getClaim('sub'));

            if (! $user) {
                throw new ApiException('invalid_user');
            }

            return $user;
        }
        catch (\Lindelius\JWT\Exception\Exception $e) {
            throw new ApiException('invalid_jwt', $e->getMessage());
        }
    }
}