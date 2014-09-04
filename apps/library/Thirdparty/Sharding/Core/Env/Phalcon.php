<?php 

use Core\Model;

namespace Sharding\Core\Env;

use Core\Utils as _U,
    Sharding\Core\Loader as Loader,
	Sharding\Core\Model\Model as Model,
	Sharding\Core\Env\Helper\THelper as Helper;

	
trait Phalcon
{
	use Helper;
	
	public static $targetShardCriteria	= false;
	public static $needTargetShard		= true;
	public $app							= false;
	public $destinationId				= false;
	public $destinationDb				= false;
	public $destinationTable			= false;
	public $modeStrategy				= false;

	
	public function onConstruct()
	{
		$this -> app = new Loader();
		parent::onConstruct();
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
			_U::dump('shard criteria must be setted');
			//throw new Exception('shard criteria must be setted');
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

		return $newObject;		
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
		if (!self::$targetShardCriteria && self::$needTargetShard) {
			_U::dump('shard criteria must be setted');
			//throw new Exception('shard criteria must be setted');
			return false;
		}
		$result = parent::find($parameters);
		
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
	 * Override Phalcon\Mvc\Model update() method.
	 * 
	 * @access public
	 * @param array $data
	 * @param array $whitelist
	 * @return boolean
	 */
	public function update($data = NULL, $whiteList = NULL)
	{
_U::dump('ooops, what the fuck are you doing here, bastard?');	
	}

	
	/**
	 * Override Phalcon\Mvc\Model delete() method.
	 * 
	 * @access public
	 * @return boolean
	 */
	public function delete()
	{
	
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
	
	
	public function unsetNeedShard()
	{
		self::$needTargetShard = false;
	}
}