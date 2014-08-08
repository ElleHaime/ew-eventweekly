<?php 

namespace Sharding\Core\Loader;

use Core\Utils as _U;

class Config
{
	public $config;
	public $serviceConfig;
	public $connections;
	
	public function __construct()
	{
		$confpath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'config.php';
		$config = include $confpath;
		$this -> config = json_decode(json_encode($config), FALSE);
		
		$confpath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'serviceConfig.php';
		$config = include $confpath;
		$this -> serviceConfig = json_decode(json_encode($config), FALSE);
		
		$this -> init();
	}
	
	public function init()
	{
		$this -> loadConnections();
		$this -> loadShardModels();
	}
	
	protected function loadConnections()
	{
		$this -> connections = new \stdClass();
		
		foreach ($this -> config -> connections as $conn => $data) 
		{
			$adapterName = ucfirst(strtolower($data -> adapter));
			$factoryName = ucfirst(strtolower($data -> adapter)) . 'Factory';
			$instanceName = '\Sharding\Core\Adapter\\' . $adapterName . '\\' . $factoryName;
			$instance = new $instanceName();

			$this -> connections -> $conn = $instance -> addConnection($data);
		}
	}
	
	protected function loadShardModels()
	{
		foreach ($this -> config -> shardModels as $model => $data) {
			if ($data -> shards) {
				foreach ($data -> shards as $db => $shard) {			
					foreach ($this -> connections as $conn) {
						if (!$conn -> tableExists('shard_mapper_' . $model)) {
							$shardType = $shard -> shardType;
							$driver = $conn -> getDriver();
							$conn -> createTable('shard_mapper_' . $model, $this -> serviceConfig -> mode -> $shardType -> schema -> $driver);  
						} 
					}
				}
			}
		}		
		_U::dump($this -> connections);
	} 
}