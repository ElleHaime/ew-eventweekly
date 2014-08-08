<?php 

namespace Sharding\Core\Adapter;

abstract class AdapterAbstract
{
	protected $connection;
	protected $errors;
	protected $writeable;
	
	abstract function connect();
	
	abstract function getDriver();
	
	abstract function createTable($tblName, $data);
	
	abstract function tableExists($tableName);
}