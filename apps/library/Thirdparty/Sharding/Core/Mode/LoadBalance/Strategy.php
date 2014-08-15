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
_U::dump($this -> shardEntity, true);		
_U::dump($this -> shardModel, true);
_U::dump($arg);
		
		$mapper = new Map($this -> shardEntity);
		$mapper -> useConnection($this -> config -> masterConnection);
		$shard = $mapper -> findByCriteria($arg);

		// create new shard or use existed		
		if (!$shard) {
			
			// check number of tables in each available connection 
			foreach ($this -> shardModel -> shards as $conn => $data) {
				$mapper -> useConnection($conn);
				
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

