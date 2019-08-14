<?php

namespace Tnt\Account\Facade;

use Oak\Facade;
use Tnt\Account\Authentication;

class Auth extends Facade
{
	protected static function getContract(): string
	{
		return Authentication::class;
	}
}