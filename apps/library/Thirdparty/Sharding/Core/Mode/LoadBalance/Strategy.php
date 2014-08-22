<?php

namespace Sharding\Core\Mode\Loadbalance;

use Sharding\Core\Mode\StrategyAbstract,
	Core\Utils as _U,
    Sharding\Core\Loader as Loader,
	Sharding\Core\Mode\Loadbalance\Map as Map,
	Sharding\Core\Mode\Loadbalance\Shard as Shard;


class Strategy extends StrategyAbstract
{
	protected $shardSelected;	
	protected $shardsAvailable;

	
	/**
	 * Search shard by criteria. If shard not found (was passed new criteria), 
	 * then compare available shards and return the pair connection+table
	 * with min records.
	 * 
	 * @access public
	 * @param int|string @arg
	 * @return array
	 */
	public function getShard($arg)
	{
		$mapper = new Map($this -> app);
		$mapper -> setEntity($this -> shardEntity);
		$mapper -> useConnection($this -> app -> getMasterConnection());
		$shardSelected = $mapper -> findByCriteria($arg);

		// create new shard or use existed		
		if (!$shardSelected) {
			$sharder = new Shard($this -> app);
			
			// check number of rows in all tables for each available connection 
			foreach ($this -> shardModel -> shards as $conn => $data) {
				$sharder -> useConnection($conn);
				$this -> shardsAvailable[$conn] = $sharder -> compareShardTables($data);
			}
			
			_U::dump($this -> shardsAvailable);
			// select optimal shard with minimum rows
			// TODO: add comparison between connections
			// add record about new location of criteria to the map table
			  
		} 

		return $shardSelected;
	}
}

