<?php

namespace Core;

use Core\Utils as _U;

class Model extends \Phalcon\Mvc\Model
{
	public $di;
	public $geo;
	public $modelsManager;


	public function onConstruct()
	{
		$this -> di = $this -> getDI();
		$this -> geo = $this -> di -> get('geo');
		$this -> modelsManager = $this -> di -> get('modelsManager');
	}

	public function createOnChange($argument)
	{
		return false;
	}
}