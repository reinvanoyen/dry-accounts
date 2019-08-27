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
		$this->temp_token = null;
		$this->save();

		Dispatcher::dispatch(Activated::class, new Activated($this));
	}

	public function save()
	{
		if (! $this->id) {

			$this->created = time();
			$this->updated = time();
			$this->password_salt = \dry\util\string\random(10);
			$this->password = md5($this->password.$this->password_salt);
			$this->temp_token = \dry\util\string\random(10);
			parent::save();

			Dispatcher::dispatch(Created::class, new Created($this));
			return;
		}

		$this->updated = time();
		parent::save();
	}
}