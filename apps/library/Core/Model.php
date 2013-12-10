<?php

namespace Core;

use Core\Utils as _U;

class Model extends \Phalcon\Mvc\Model
{
	public $modelsManager;

	
	public function onConstruct()
	{
		$di = $this -> getDi();
		$this -> modelsManager = $di -> get('modelsManager');
	}

	public function createOnChange($argument)
	{
		return false;
	}
	
	protected function getConfig()
	{
		$config = $this -> getDi() -> get('config');
		return  $config;
	}
	
	protected function getGeo()
	{
		$geo = $this -> getDi() -> get('geo');
		return  $geo;
	}
}