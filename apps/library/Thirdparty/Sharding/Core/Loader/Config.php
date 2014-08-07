<?php 

namespace Sharding\Core\Loader;

use Core\Utils as _U;

class Config
{
	public $config;
	public $connections;
	
	public function __construct()
	{
		$confpath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'config.php';
		$config = include $confpath;
		$this -> config = json_decode(json_encode($config), FALSE);
		
		$this -> init();
	}
	
	public function init()
	{
		$this -> loadConnections();
	}
	
	public function loadConnections()
	{
		foreach ($this -> config -> connections as $conn => $data) 
		{
			$this -> connections -> $conn = new 
			_U::dump($conn, true);
			_U::dump($data);
		}
	}
}