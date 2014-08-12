<?php 

namespace Sharding\Phalcon;

use Core\Utils as _U,
    Sharding\Core\Loader\Config as Config;

trait Phalcon
{
	public static $shardQueryParams = [];
	
	
	public static function find($parameters = NULL)
	{
		
	}
	
	public static function findFirst($parameters = NULL)
	{
		$config = new Config();

		$className = get_class();
		$object = new \ReflectionClass(get_class());
		$entityName = $object -> getShortName();
		$shardModel = $config -> loadShardModel($entityName);
		
		_U::dump($shardModel);
				
	}
	
	public function save($data = NULL, $whiteList = NULL) 
	{
		
	}
	
	public function update($data = NULL, $whiteList = NULL)
	{
	
	}
	
	public function delete()
	{
	
	}
	
	public function getReadConnection()
	{
		
	}
	
	public function getWriteConneciton()
	{
		
	}
}