<?php 

namespace Sharding\Core\Mode;

use Core\Utils as _U;

abstract class StrategyAbstract
{
	public $app;
	protected $shardModel;
	protected $shardEntity;

	
	public function __construct($app)
	{
		$this -> app = $app;
	}

	public function setShardModel($model)
	{
		$this -> shardModel = $model;
	}
	
	public function setShardEntity($entity)
	{
		$this -> shardEntity = strtolower($entity);
	}
	
	abstract public function selectShard($arg);
}