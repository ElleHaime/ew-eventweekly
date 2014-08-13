<?php 

use Core\Model;

namespace Sharding\Phalcon;

use Core\Utils as _U,
    Sharding\Core\Loader\Config as Config;

trait Phalcon
{
	protected $targetShardCriteria = NULL;
	protected $shardConfig;
	protected $shardQueryParams = [];
	protected $destinationDb;
	protected $destinationTable;
	protected $searchType;
	protected $shardModel;

	public function initialize()
	{
		$this -> shardConfig = new Config();
		
		parent::initialize();
	}

	
	public static function find($parameters = NULL)
	{
		
	}
	
	public static function findFirst($parameters = NULL)
	{
		$shardConfig = new Config();
		$object = new \ReflectionClass(__CLASS__);
		$entityName = $object -> getShortName();
		
		if ($shardModel = $shardConfig -> loadShardModel($entityName)) { 
			$objName = '\\' . __CLASS__;
			$obj = new $objName;
			$result = $obj -> shardingSearchOne($entityName, $shardModel, $parameters);
		} else {
			$result = $obj -> defaultSearch($entityName, $parameters);
		}

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
	
	/**
	 * Search not-sharded data in the default shard
	 * 
	 * @access public
	 * @param string $entityName
	 * @param string $parameters
	 * @return Model|false Phalcon model object 
	 */
	public function defaultSearch($entityName, $parameters = NULL)
	{
		
	}

	
	public function shardingSearchOne($entityName, $shardModel, $parameters = NULL)
	{
		$params = $this -> processShardArguments($parameters);
		$this -> setReadDestination($entityName, $shardModel, $parameters);
	}

	
	public function shardingSearchAll($entityName, $shardModel, $parameters = NULL)
	{
		if (!isset($this -> targetShardCriteria)) {
			_U::dump('oooops');
			throw new \Exception('Sharding criteria required');
		}
_U::dump($shardModel);
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
		
_U::dump($shard);
		
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
	
	public function setShardCriteria($criteria)
	{
		$this -> targetShardCriteria = $criteria;
	}
}