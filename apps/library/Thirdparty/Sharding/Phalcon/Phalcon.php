<?php 

namespace Sharding\Phalcon;

use Core\Utils as _U,
    Sharding\Core\Loader\Config as Config;

trait Phalcon
{
	protected $shardQueryParams = [];
	protected $destinationDb;
	protected $destinationTable;
	protected $searchType;
	protected $shardModel;
	
	
	public static function find($parameters = NULL)
	{
	}
	
	public static function findFirst($parameters = NULL)
	{
		$config = new Config();

		$object = new \ReflectionClass(__CLASS__);
		$entityName = $object -> getShortName();
		$shardModel = $config -> loadShardModel($entityName);
		
		$objName = '\\' . __CLASS__;
		$obj = new $objName;
		//$params = $obj -> processShardArguments($parameters);
		//$obj -> setReadDestination($entityName, $shardModel, $parameters);
		
		$result = $obj -> searchInShards($entityName, $shardModel, $parameters);
_U::dump($result);		
		return $result;
	}
	
	public function save($data = NULL, $whiteList = NULL) 
	{
		
	}
	
	public function update($data = NULL, $whiteList = NULL)
	{
	
	}
	
	public function delete()
	{
	
	}
	
	
	public function searchInShards($entityName, $shardModel, $parameters = NULL)
	{
		$params = $this -> processShardArguments($parameters);
		$this -> setReadDestination($entityName, $shardModel, $parameters);
	}
	
	
	public function processShardArguments($parameters = NULL)
	{
		$args = [];

		if (is_integer($parameters) || is_string($parameters)) {
			$args = ['id' => $parameters]; 
		} elseif (is_array($parameters)) {
			$args = $parameters;
		} 
		
		return $args;
	}
	
	
	public function setReadDestination($entity, $model, $args)
	{
		$modeName = '\Sharding\Core\Mode\\' . ucfirst($model -> shardType) . '\Strategy';
		$mode = new $modeName;
		
		$shard = $mode -> getShard($entity, $args);
		$this -> destinationDb = $shard;
		$this -> destinationDb = $shard;
		$this -> destinationTable = 'event';
	}
		
	public function getReadConnection()
	{
		return $this -> destinationDb;
	}
	
	public function getWriteConneciton()
	{
		return $this -> destinationDb;
	}
	
	public function setSource($table = 'event')
	{
		return $tthis -> destinationTable;
	}
}