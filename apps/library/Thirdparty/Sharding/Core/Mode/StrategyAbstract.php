<?php 

namespace Sharding\Core\Mode;

use Core\Utils as _U,
	Sharding\Core\Loader as Loader;

abstract class StrategyAbstract
{
	public $config;
	protected $shardModel;
	protected $shardEntity;

	
	public function __construct()
	{
		$this -> config = new Loader();
	}

	public function setShardModel($model)
	{
		$this -> shardModel = $model;
	}
	
	public function setShardEntity($entity)
	{
		$this -> shardEntity = strtolower($entity);
	}
	
	abstract public function getShard($arg);
}