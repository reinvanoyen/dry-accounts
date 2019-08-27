<?php

namespace Tnt\Account\Facade;

use Oak\Facade;
use Tnt\Account\Contracts\AuthenticationInterface;

class Auth extends Facade
{
	protected static function getContract(): string
	{
		return AuthenticationInterface::class;
	}
}