<?php

namespace Sharding\Core\Mode\Loadbalance;

use Sharding\Core\Mode\StrategyAbstract,
	Core\Utils as _U,
    Sharding\Core\Loader as Loader,
	Sharding\Core\Mode\Loadbalance\Map as Map,
	Sharding\Core\Mode\Loadbalance\Shard as Shard;


class Strategy extends StrategyAbstract
{
	protected $shardsAvailable	= [];
	protected $shardDbname			= false;
	protected $shardTblname			= false;
	protected $shardId				= false;

	
	/**
	 * Search shard by criteria. If shard not found (was passed new criteria), 
	 * then compare available shards and return the pair connection+table
	 * with min records.
	 * 
	 * @access public
	 * @param int|string @arg
	 * @return array
	 */
	public function selectShard($arg)
	{
		$mapper = new Map($this -> app);
		$mapper -> setEntity($this -> shardEntity);
		$mapper -> useConnection($this -> app -> getMasterConnection());
		$mapper -> findByCriteria($arg);

		// create new shard or use existed
		if ($mapper -> id) {
			$this -> shardDbname = $mapper -> dbname;
			$this -> shardTblname = $mapper -> tblname;
			$this -> shardId = $mapper -> id;
		} else {
			$sharder = new Shard($this -> app);
			
			// check number of rows in all tables for each available connection 
			foreach ($this -> shardModel -> shards as $conn => $data) {
				$sharder -> useConnection($conn);
				$this -> shardsAvailable[] = ['connection' => $conn,
											  'table' => $sharder -> getMinTable($data)];
			}

			// select optimal shard with minimum rows
			// TODO: add comparison between connections
			$newShard = $this -> shardsAvailable[0];
			$newShard['criteria'] = $arg;
			// add record about new location of criteria to the map table
			$this -> addShard($newShard);
		} 

		return;
	}

	/**
	 * Create new record to the mapping table
	 * 
	 * @access private
	 * @param array @arg
	 * @return int 
	 */
	private function addShard($newShard)
	{
		$mapper = new Map($this -> app);
		$mapper -> setEntity($this -> shardEntity);
		$mapper -> useConnection($this -> app -> getMasterConnection());
			
		$mapper -> criteria = $newShard['criteria'];
		$mapper -> dbname = $newShard['connection'];
		$mapper -> tblname = $newShard['table'];
		
		$result = $mapper -> save();
		if ($result) {
			$this -> shardDbname = $mapper -> dbname;
			$this -> shardTblname = $mapper -> tblname;
			$this -> shardId = $mapper -> id;
		} 
		
		return;
	}
	
	public function getDbName()
	{
		return $this -> shardDbname; 	
	} 
	
	public function getTableName()
	{
		return $this -> shardTblname;
	}
	
	public function getId()
	{
		return $this -> shardId;
	}
}

