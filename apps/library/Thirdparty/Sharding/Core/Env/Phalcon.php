<?php 

use Core\Model;

namespace Sharding\Core\Env;

use Core\Utils as _U,
    Sharding\Core\Loader as Loader,
	Sharding\Core\Model\Model as Model,
	Sharding\Core\Env\Helper\THelper as Helper,
	\Exception as Exception;

	
trait Phalcon
{
	use Helper;
	
	public static $targetShardCriteria	= false;
	public static $convertationMode		= false;
	public static $needTargetShard		= true;
	
	public $app							= false;
	public $destinationId				= false;
	public $destinationDb				= false;
	public $destinationTable			= false;
	public $modeStrategy				= false;
	
	public $relationOf					= false;

	
	public function onConstruct()
	{
		if (!$config = $this -> getDi() -> get('shardingConfig')) {
			throw new Exception('Sharding config not found');
			return false; 
		}
		if (!$serviceConfig = $this -> getDi() -> get('shardingServiceConfig')) {
			throw new Exception('Sharding service config not found');
			return false; 
		}
		
		$this -> app = new Loader($config, $serviceConfig);
		
		if ($relation = $this -> getRelationByObject()) {
			$this -> setShardByParent($relation);
		}  
	}

	
	/**
	 * Override Phalcon\Mvc\Model save() method.
	 * 
	 * @access public
	 * @param array $data
	 * @param array $whitelist
	 * @return Phalcon\Mvc\Model object|false
	 */
	public function save($data = NULL, $whiteList = NULL)
	{
		if (self::$targetShardCriteria === false) {
			throw new Exception('shard criteria must be setted');
			return false; 
		}

		$reflection = new Model($this -> app);
		$reflection -> setConnection($this -> destinationDb);
		$reflection -> setEntity($this -> destinationTable);
		$reflectionFields = $reflection -> getEntityStructure(); 

		foreach(get_object_vars($this) as $prop => $value) {
			if (isset($reflectionFields[$prop])) {
				if ($value == '') {
					$value = NULL;
				}
				$reflectionFields[$prop]['value'] = $value;			
			}
		}
		$newObject = $reflection -> save($reflectionFields, $this -> destinationId);
		$this -> id = $newObject;
		
		return $this;		
	}

	
	/**
	 * Override Phalcon\Mvc\Model::find() method.
	 * 
	 * @access public static
	 * @param $parameters
	 * @return Phalcon\Mvc\Model\Resultset\Simple object|false
	 */
	public static function find($parameters = NULL)
	{
		if (self::$targetShardCriteria === false && self::$needTargetShard && !self::$convertationMode) {
			throw new Exception('shard criteria must be setted');
			return false;
		} else {
			// fetch data from shard
			$result = parent::find($parameters);
		}
		
		return $result; 
	}

	
	/**
	 * Override Phalcon\Mvc\Model::findFirst() method.
	 * 
	 * @access public static
	 * @param $parameters
	 * @return Phalcon\Mvc\Model\Resultset\Simple object|false
	 */
	public static function findFirst($parameters = NULL)
	{
		if (!is_null($parameters)) {
			// search by primary id. Example: findFirst(123)
			if (!strpos($parameters, '=')) {
				$result = parent::findFirst('id = "' . $parameters . '"');
			} else {
				$result = parent::findFirst($parameters);
			}
		}
		
		return $result; 
	}

	
	/**
	 * Set read connection. 
	 * Use Phalcon\Mvc\Model setReadConnectionService() 
	 *
	 * @access public
	 */
	public function setReadDestinationDb()
	{
		$this -> setReadConnectionService($this -> destinationDb);
	}
	

	/**
	 * Set write connection. 
	 * Use Phalcon\Mvc\Model setWriteConnectionService() 
	 *
	 * @access public
	 */
	public function setWriteDestinationDb()
	{
		$this -> setWriteConnectionService($this -> destinationDb);
	}
	
	
	/**
	 * Set shard table
	 * Use Phalcon\Mvc\Model setSource()
	 * 
	 * @access public
	 */
	public function setDestinationSource()
	{
		$this -> setSource($this -> destinationTable);
	}
	
	
	/**
	 * Set shard in models manager. 
	 * For search in all shards  
	 * Use Phalcon\Mvc\Model setModelSource()
	 *
	 * @access public
	 */
	public function getModelsManager()
	{
		$mngr = parent::getModelsManager();

		if (!is_null($this -> id) && !self::$convertationMode) {
			$mngr -> __destruct();
			$mngr -> setModelSource($this, $this -> destinationTable);
		}
		
		return $mngr;
	}

	
	/**
	 * Override 'magic' Phalcon\Mvc\Model __get(). Return related records using 
	 * the relation alias as a property 
	 * If requested property is sharded relation, then return related records using
	 * from shard table, if not -- from default table of the current proprty. 
	 * 
	 * @access public
	 * @param string $property
	 * @return false|object property   
	 */
	public function __get($property)
	{
		if (!is_null($this -> id) && !self::$convertationMode && $this -> getRelationByProperty($property)) {
			$this -> setShardById($this -> id);
		} 
		
		return parent::__get($property);
	}

	
	/**
	 * Override 'magic' Phalcon\Mvc\Model __isset(). Check if a property is a valid 
	 * relation
	 *
	 * @access public
	 * @param string $property
	 * @return boolean
	 */
	public function __isset($property)
	{
		if (!is_null($this -> id) && !self::$convertationMode) {
			$this -> setShardById($this -> id);
		} 
	
		return parent::__isset($property);
	}
	
	
	/**
	 * Fucking shame, I'm sorry. For convertation to sharded structure only.
	 * Here we fetch parent id for related model. 
	 */
	protected function setShardByParent($relation)
	{
		$trace = debug_backtrace();
		$callsNum = count($trace);
		$callArgs = false;
				
		for ($i = 0; $i < $callsNum; $i++) {
			if ($trace[$i]['function'] == 'getRelationRecords') {
				$callArgs = $trace[$i]['args'];
				break;
			}
		}
		
		if ($callArgs) {		
			$parent = $this -> relationOf;
			$parentPrimary = $this -> app -> config -> shardModels -> $parent -> primary;
			$parentId = $callArgs[2] -> $parentPrimary;
			
			if ($parentId) {
				$this -> setShardById($parentId);
			}
		} else {
			$this -> setShardByDefault($relation);
		}
	}
}