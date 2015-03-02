<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Featured extends Model
{
	public $id;
	public $object_type;
	public $object_id;
	public $priority;
}