<?php

namespace Tnt\Account\Controller;

use function dry\util\string\random;
use Lindelius\JWT\Exception\ExpiredJwtException;
use Oak\Contracts\Config\RepositoryInterface;
use Tnt\Account\Contracts\AuthenticatableInterface;
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

    public function authenticate(Request $request)
    {
        if (! $request->getHeader('USER') || ! $request->getHeader('PASSWORD')) {
            throw new ApiException('auth_failed');
        }

        if (! $this->authentication->authenticate($request->getHeader('USER'), $request->getHeader('PASSWORD'))) {
            throw new ApiException('auth_failed');
        }

        $user = $this->authentication->getUser();

        return $this->createToken($user);
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
        catch (ExpiredJwtException $e) {
            throw new ApiException('expired_jwt', $e->getMessage());
        }
        catch (\Lindelius\JWT\Exception\Exception $e) {
            throw new ApiException('invalid_jwt', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws ApiException
     */
    public function refreshToken(Request $request)
    {
        $user = $this->userRepository->withValidRefreshToken($request->data->json('refresh_token'));

        if (! $user) {
            throw new ApiException('invalid_user');
        }

        return $this->createToken($user);
    }

    /**
     * @param AuthenticatableInterface $user
     * @return array
     * @throws ApiException
     */
    private function createToken(AuthenticatableInterface $user): array
    {
        try {

            $jwt = new \Lindelius\JWT\StandardJWT();
            $jwt->exp = $this->config->get('accounts.token_expiry_time', time() + (60 * 60));
            $jwt->iat = time();
            $jwt->sub = $user->getIdentifier();

            $user->refresh_token = random(16);
            $user->refresh_token_expiry_time = $this->config->get('accounts.refresh_token_expiry_time', time() + (60*60*2));
            $user->save();

            return [
                'access_token' => $jwt->encode($this->secret),
                'refresh_token' => $user->refresh_token,
                'expires_at' => $user->refresh_token_expiry_time,
            ];

        }
        catch (\Lindelius\JWT\Exception\Exception $e) {
            throw new ApiException('invalid_jwt', $e->getMessage());
        }
    }
}