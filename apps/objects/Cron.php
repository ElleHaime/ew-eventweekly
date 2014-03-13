<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Cron extends \Phalcon\Mvc\Model
{
	public $id;
	public $name;
	public $description;
	public $path;
	public $member_id;
	public $parameters;
	public $state;
}
