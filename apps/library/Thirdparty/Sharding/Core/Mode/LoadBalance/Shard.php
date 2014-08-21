<?php 

namespace Sharding\Core\Mode\Loadbalance;

use Core\Utils as _U,
	Sharding\Core\Loader as Loader,
	Sharding\Core\Mode\ShardAbstract as ShardAbstract;

class Shard
{
	public $entity;
	public $connection;
	public $app;
	
	public function __construct($app)
	{
		$this -> app = $app;
	}
	
	public function createShard()
	{
	
	}
	
	public function useConnection($conn)
	{
		$this -> connection = $this -> app -> connections -> $conn;
	}
	
	public function setEntity($entity)
	{
		$this -> entity = $entity;
	}
}
