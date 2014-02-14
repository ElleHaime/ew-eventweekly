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
	public $parameters;
	public $state;
}
