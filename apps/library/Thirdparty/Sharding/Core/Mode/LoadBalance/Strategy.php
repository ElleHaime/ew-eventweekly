<?php

namespace Sharding\Core\Mode\Loadbalance;

use Sharding\Core\Mode\StrategyAbstract,
	Core\Utils as _U,
    Sharding\Core\Loader as Loader,
	Sharding\Core\Mode\Loadbalance\Map as Map,
	Sharding\Core\Mode\Loadbalance\Shard as Shard;


class Strategy extends StrategyAbstract
{
	private $shardSelected 		= false;
	public $shardsAvailable		= [];
	
	public function getShard($arg)
	{
		$mapper = new Map($this -> app);
		$mapper -> setEntity($this -> shardEntity);
		$mapper -> useConnection($this -> app -> getMasterConnection());
		$shard = $mapper -> findByCriteria($arg);
_U::dump($this -> shardModel);

		// create new shard or use existed		
		if (!$shard) {
			$sharder = new Shard($this -> app);
			$sharder -> setEntity($this -> shardEntity);
			
			// check number of tables in each available connection 
			foreach ($this -> shardModel -> shards as $conn => $data) {
				$sharder -> useConnection($conn);
				
				$this -> shardsAvailable[$conn] = $mapper -> getShardsData();
				if ($this -> shardsAvailable[$conn] -> tablesExist < $data -> tablesMax) {
					$this -> shardSelected = true;
					break;
				} 
			}
			
			if (!$this -> shardSelected) {
				// select shard with table with minimum rows
			}
		} 

		return $shard;
	}
}

