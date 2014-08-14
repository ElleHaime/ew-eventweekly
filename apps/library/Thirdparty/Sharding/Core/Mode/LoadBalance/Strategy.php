<?php

namespace Sharding\Core\Mode\Loadbalance;

use Sharding\Core\Mode\StrategyAbstract,
	Core\Utils as _U,
    Sharding\Core\Loader\Config as Config,
	Sharding\Core\Mode\Loadbalance\Mapper as Mapper;

class Strategy extends StrategyAbstract
{
	public function getShard($arg)
	{
_U::dump($this -> shardModel);		
		$mapper = new Mapper($this -> shardEntity);
		$mapper -> useDefaultConnection();
		$shard = $mapper -> findByCriteria($arg);

		// create new shard or use existed		
		if (!$shard) {
			// check number of tables in each 
			foreach ($this -> shardModel -> shards as $conn => $data) {
				$tablesCount = $mapper -> getTablesAmout($conn);
				if ($tablesCount < $data -> tablesMax) {
					$mapper -> setWorkingConnection($conn);
				} 
			}
		} 

		return $shard;
	}
}

