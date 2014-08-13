<?php 

namespace Sharding\Core\Loader;

use Core\Utils as _U,
	Sharding\Core\Mode\Loadbalance\Mapper as LoadbalancerMapper,
	Sharding\Core\Mode\Limitbatch\Mapper as LimitbatchMapper,
	Sharding\Core\Mode\Oddeven\Mapper as OddevenMapper;

class Config
{
	public $config;
	public $serviceConfig;
	public $connections;
	public $shardModels;
	
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
		$this -> loadShardMappers();
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
	
	protected function loadShardMappers()
	{
//TODO: move this shit from here		
		foreach ($this -> config -> shardModels as $model => $data) {
			if ($data -> shards) {
				foreach ($data -> shards as $db => $shard) {			
					foreach ($this -> connections as $conn) {
						if (!$conn -> tableExists('shard_mapper_' . strtolower($model))) {
							$shardType = $data -> shardType;
							$driver = $conn -> getDriver();
							$conn -> createShardTable('shard_mapper_' . strtolower($model), $this -> serviceConfig -> mode -> $shardType -> schema -> $driver);  
						} 
					}
				}
			}
		}		
		
		/*
		 * Model Object
		 * - entity
		 * - criteria
		 * - connections
		 */
	} 
	
	
	public function loadShardModel($entity)
	{
		if (isset($this -> config -> shardModels -> $entity)) {
			return $this -> config -> shardModels -> $entity;
		} else {
			return false;			
		}
	}
}