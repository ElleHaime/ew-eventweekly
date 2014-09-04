<?php

use Core\Model;

namespace Sharding\Core\Env\Helper;

use Core\Utils as _U;

trait THelper
{
	/**
	 * Set default connection for a non-sharded models
	 * 
	 * @access public
	 */
	public function useDefaultConnection()
	{
		$this -> destinationDb = $this -> app -> connections($this -> app -> getDefaultConnection());
		
		$this -> setReadDestinationDb();
		$this -> setWriteDestinationDb();
	}

	
	/**
	 * Select destination shard by shard id
	 * 
	 * @param int $objectId
	 * @access public
	 */
	public function setShardById($objectId)
	{
		$shardId = $this -> parseShardId($objectId);
		$this -> selectModeStrategy();
		
		if ($this -> modeStrategy) {
			$this -> modeStrategy -> selectShardById($shardId);
			
			self::$targetShardCriteria = $this -> modeStrategy -> getCriteria();
			$this -> destinationId = $this -> modeStrategy -> getId();
			$this -> destinationDb = $this -> modeStrategy -> getDbName();
			$this -> destinationTable = $this -> modeStrategy -> getTableName();
			
			$this -> setDestinationSource();
		} else {
			$this -> useDefaultConnection();
		}
		
		$this -> setReadDestinationDb();
	}

	
	/**
	 * Select destination shard by criteria
	 * 
	 * @param int|string $criteria
	 * @access public
	 */
	public function setShardByCriteria($criteria)
	{
		self::$targetShardCriteria = $criteria;
		$this -> selectModeStrategy();
	
		if ($this -> modeStrategy) {
			$this -> modeStrategy -> selectShardByCriteria(self::$targetShardCriteria);
			$this -> destinationId = $this -> modeStrategy -> getId();
			$this -> destinationDb = $this -> modeStrategy -> getDbName();
			$this -> destinationTable = $this -> modeStrategy -> getTableName();
			
			$this -> setDestinationSource();
		} else {
			$this -> useDefaultConnection();
		}
		
		$this -> setReadDestinationDb();
	}


	
	/**
	 * Parse shard id from object's primary key.
	 * For sharded models only 
	 *
	 * @access public 
	 * @param string $objectId 
	 * @return int|string
	 */
	public function parseShardId($objectId)
	{
		$separator = $this -> app -> getShardIdSeparator();
		
		$idParts = explode($separator, $objectId);
		if ($idParts) {
			return $idParts[1];
		} else {
			return false;
		}
	}

	
	/**
	 * Select strategy mode (Loadbalance, Limitbatch) for 
	 * specific model
	 *
	 * @access public 
	 */
	public function selectModeStrategy()
	{
		$object = new \ReflectionClass(__CLASS__);
		$entityName = $object -> getShortName();

		if ($shardModel = $this -> app -> loadShardModel($entityName)) {
			$modeName = '\Sharding\Core\Mode\\' . ucfirst($shardModel -> shardType) . '\Strategy';
			$this -> modeStrategy = new $modeName($this -> app);
			$this -> modeStrategy -> setShardEntity($entityName);
			$this -> modeStrategy -> setShardModel($shardModel);
		}
	}
}