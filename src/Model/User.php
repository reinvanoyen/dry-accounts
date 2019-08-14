<?php

namespace Tnt\Account\Model;

use dry\orm\Model;
use dry\orm\special\Boolean;
use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Events\Activated;
use Tnt\Account\Events\Created;

class User extends Model
{
	const TABLE = 'user';

	public static $special_fields = [
		'is_activated' => Boolean::class,
	];

	public function activate()
	{
		$this->is_activated = true;
		$this->token = null;
		$this->save();

		Dispatcher::dispatch(Activated::class, new Activated($this));
	}

	public function save()
	{
		if (! $this->id) {

			$this->salt = \dry\util\string\random(10);
			$this->password = md5($this->password.$this->salt);
			$this->token = \dry\util\string\random(10);

			Dispatcher::dispatch(Created::class, new Created($this));
		}

		parent::save();
	}
}