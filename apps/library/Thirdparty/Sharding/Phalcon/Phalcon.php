<?php 

use Core\Model;

namespace Sharding\Phalcon;

use Core\Utils as _U,
    Sharding\Core\Loader as Loader;

trait Phalcon
{
	public $app;
	
	protected $targetShardCriteria = NULL;
	protected $shardQueryParams = [];
	protected $destinationDb;
	protected $destinationTable;
	protected $searchType;
	protected $shardModel;

	
	public function onConstruct()
	{
		$this -> app = new Loader();
		parent::onConstruct();
	}
	

	public function save($data = NULL, $whiteList = NULL)
	{
		$object = new \ReflectionClass(__CLASS__);
		$entityName = $object -> getShortName();

		if ($shardModel = $this -> app -> loadShardModel($entityName)) {
			$modeName = '\Sharding\Core\Mode\\' . ucfirst($shardModel -> shardType) . '\Strategy';
			$modeStrategy = new $modeName;
			$modeStrategy -> setShardEntity($entityName);
			$modeStrategy -> setShardModel($shardModel);

			$shard = $modeStrategy -> getShard($this -> location_id);
		} else {
			
		}
_U::dump($this -> location_id, true);		
_U::dump($shardModel, true);
_U::dump($shard);
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
			throw new \Exception('Sharding criteria required');
		}

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
		return $this -> destinationTable;
	}
	
	public function setShardCriteria($criteria)
	{
		$this -> targetShardCriteria = $criteria;
	}
}