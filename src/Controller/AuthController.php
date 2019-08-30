<?php

namespace Tnt\Account\Controller;

use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\ExternalApi\Exception\ApiException;
use Tnt\ExternalApi\Http\Request;
use Tnt\Account\Facade\Auth as Authentication;

const AUTH_SECRET = '813D1494ACC6FFFC1F117C8A899242B89DD4FE7F4EE153BE2CE211F54F00EFED';

class AuthController
{
	/**
	 * @var UserRepositoryInterface
	 */
	private $userRepository;

	/**
	 * AuthController constructor.
	 * @param UserRepositoryInterface $userRepository
	 */
	public function __construct(UserRepositoryInterface $userRepository)
	{
		$this->userRepository = $userRepository;
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

		if (! Authentication::authenticate($request->getHeader('USER'), $request->getHeader('PASSWORD'))) {
			throw new ApiException('auth_failed');
		}

		$user = Authentication::getUser();

		try {

			$jwt = new \Lindelius\JWT\StandardJWT();
			$jwt->exp = time() + (60 * 60); // Expire after one hour
			$jwt->iat = time();
			$jwt->sub = $user->getIdentifier();

			return $jwt->encode(AUTH_SECRET);

		}
		catch (\Lindelius\JWT\Exception\Exception $e) {
			throw new ApiException('invalid_jwt', $e->getMessage());
		}
	}

	/**
	 * @param Request $request
	 * @throws ApiException
	 */
	public function authorize(Request $request)
	{
		if (! $request->getHeader('AUTHORIZATION')) {
			throw new ApiException('authorize_failed');
		}

		try {

			$decodedJwt = \Lindelius\JWT\StandardJWT::decode($request->getHeader('AUTHORIZATION'));
			$decodedJwt->verify(AUTH_SECRET);

			$user = $this->userRepository->withIdentifier($decodedJwt->getClaim('sub'));

			if (! $user) {
				throw new ApiException('invalid_user');
			}

		}
		catch (\Lindelius\JWT\Exception\Exception $e) {
			throw new ApiException('invalid_jwt', $e->getMessage());
		}
	}
}