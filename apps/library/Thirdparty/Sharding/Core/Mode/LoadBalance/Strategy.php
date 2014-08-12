<?php

namespace Sharding\Core\Mode\Loadbalance;

use Sharding\Core\Mode\StrategyInterface,
	Core\Utils as _U,
    Sharding\Core\Loader\Config as Config,
	Sharding\Core\Mode\Loadbalance\Mapper as Mapper;

class Strategy implements StrategyInterface
{
	public $config;
	protected $shardModel;
	
	public function __construct()
	{
		$this -> config = new Config();
	}
	
	public function getShard($entity, $model, $args = [])
	{
		$result = [];
		//$this -> shardModel = $model;
		//$mapper = new Mapper();
		
		if (!empty($args) && isset($args[$this -> shardModel -> criteria])) {
			// search in shards by criteria
			$result['searchType'] = 'shard';
			//$result['connection'] = $mapper -> findShard($entity);
		} else {
			// search in all shards
			$result['searchType'] = 'all';
			//$result['connection'] = $mapper -> findAll($entity);
		}
		
		return $result;
	}
}

