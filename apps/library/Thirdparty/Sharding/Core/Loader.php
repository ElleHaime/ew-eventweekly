<?php 

namespace Sharding\Core;

use Core\Utils as _U,
	Sharding\Core\Mode\Loadbalance\Map as LoadbalancerMapper,
	Sharding\Core\Mode\Limitbatch\Map as LimitbatchMapper,
	Sharding\Core\Mode\Oddeven\Map as OddevenMapper;


class Loader
{
	public $config;
	public $serviceConfig;
	public $connections;
	public $shardModels;
	
	
	public function __construct()
	{
		$confpath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'config.php';
		$config = include $confpath;
		$this -> config = json_decode(json_encode($config), FALSE);
		
		$confpath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'serviceConfig.php';
		$config = include $confpath;
		$this -> serviceConfig = json_decode(json_encode($config), FALSE);
		
		$this -> init();
	}
	
	
	public function init()
	{
		$this -> loadConnections();
	}
	
	
	/**
	 * Load all available connections from config
	 * 
	 * @access protected
	 */
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

	
	/**
	 * Load sharding settings for the model if specified.
	 * Return false if model is non-shardable
	 * 
	 * @access public
	 * @param string $entity
	 * @return config object|false 
	 */
	public function loadShardModel($entity)
	{
		if (isset($this -> config -> shardModels -> $entity)) {
			return $this -> config -> shardModels -> $entity;
		} else {
			return false;			
		}
	}
	

	/**
	 * Return master connection (needed for replication)
	 *
	 * @access public
	 * @return PDO object|false
	 */
	public function getMasterConnection()
	{
		$master = null;
		
		if ($this -> config -> masterConnection) {
			$master = $this -> config -> masterConnection;
		} else {
			_U::dump('no master connections detected');
		}
		
		return $master;	
	}
	

	/**
	 * Return default connection (for non-shardable models).
	 * Return master connection if default connection wasn't setted
	 *
	 * @access public
	 * @return PDO object|false
	 */
	public function getDefaultConnection()
	{
		$default = null;
		
		if ($this -> config -> defaultConnection) {
			$default = $this -> config -> defaultConnection;
		} else {
			_U::dump('no master connections detected, search in master', true);
			$default = $this -> config -> masterConnection;
		}
		
		return $default;	
	}
	

	/**
	 * Return prefix for the mapping tables
	 *
	 * @access public
	 * @return string
	 */
	public function getMapPrefix()
	{
		$prefix = '';
		
		if ($this -> config -> shardMapPrefix) {
			$prefix = $this -> config -> shardMapPrefix;
		} else {
			$prefix = 'shard_map_';
		}
		
		return $prefix;
	}

	
	/**
	 * Return all created shards for the entity
	 *
	 * @access public
	 * @return string
	 */
	public function getAllShards($entity)
	{
		$shards = [];
		
		foreach ($this -> config -> shardModels -> $entity -> shards as $shard => $data) {
			for($i = 1; $i <= $data -> tablesMax; $i++) {
				$shards[] = $data -> baseTablePrefix . $i;
			}
		}
		
		return $shards;
	}

	
	/**
	 * Return primary key separator for the shardable objects
	 *
	 * @access public
	 * @return string
	 */
	public function getShardIdSeparator()
	{
		$separator = '';
		
		if ($this -> config -> shardIdSeparator) {
			$separator = $this -> config -> shardIdSeparator;
		} else {
			$separator = '_';
		}
		
		return $separator;
	}
}