<?php 

use Core\Model;

namespace Sharding\Phalcon;

use Core\Utils as _U,
    Sharding\Core\Loader as Loader,
	Sharding\Core\Model as Model;

trait Phalcon
{
	public $app;
	
	protected $targetShardCriteria = NULL;
	protected $shardQueryParams = [];
	
	public $destinationId;
	public $destinationDb;
	public $destinationTable;
	
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
			$modeStrategy = new $modeName($this -> app);
			$modeStrategy -> setShardEntity($entityName);
			$modeStrategy -> setShardModel($shardModel);

			$modeStrategy -> selectShard($this -> location_id);
		} else {
			$modeStrategy -> selectDefaultShard();
		}
		
		$this -> destinationId = $modeStrategy -> getId();
		$this -> destinationDb = $modeStrategy -> getDbName();
		$this -> destinationTable = $modeStrategy -> getTableName();
		
		$this -> setReadDestination();

		$lastObject = parent::findFirst(['limit' => 1, 'order' => 'id DESC']);
		$this -> id = $this -> composeNewId($lastObject);

		$reflection = new Model($this -> app);
		$reflection -> setConnection($this -> destinationDb);
		$reflection -> setEntity($this -> destinationTable);
		$reflectionFields = $reflection -> getEntityStructure(); 

		foreach(get_object_vars($this) as $prop => $value) {
			if (isset($reflectionFields[$prop])) {
				$reflectionFields[$prop]['value'] = $value;			
			}
		}
		$reflection -> save($reflectionFields);
		 
_U::dump($reflectionFields);
		
/*
		$objName = get_parent_class($this);
		$reflection = new $objName;
		foreach(get_object_vars($this) as $prop => $value) {
			$reflection -> $prop = $value;
		}
 
_U::dump($this -> destinationId, true);
_U::dump($this -> destinationDb, true);
_U::dump($this -> destinationTable, true);
_U::dump($this -> name, true);
_U::dump($this -> id, true); */		

		_U::dump('ready');		
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
	
	public function setReadDestination()
	{
		$this -> setReadConnectionService($this -> destinationDb);
		$this -> setSource($this -> destinationTable);
	}
	
	
	public function setWriteDestination()
	{
		$this -> setWriteConnectionService($this -> destinationDb);
		$this -> setSource($this -> destinationTable);
	}

	
	public function composeNewId($object = false)
	{
		if ($object === false) {
			 $id = '1_' . $this -> destinationId; 
		} else {
			$parts = explode('_', $object -> id);
			$id = (int)$parts[0] + 1 . '_' . $this -> destinationId;  
		}

		return $id;
	}	

	
	public function setShardCriteria($criteria)
	{
		$this -> targetShardCriteria = $criteria;
	}
}