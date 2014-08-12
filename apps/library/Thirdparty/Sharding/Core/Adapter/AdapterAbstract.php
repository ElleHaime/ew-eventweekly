<?php 

namespace Sharding\Core\Adapter;

abstract class AdapterAbstract
{
	protected $connection;
	protected $errors;
	protected $writeable;
	
	protected $host;
	protected $port;
	protected $user;
	protected $password;
	protected $database;
	
	public function __construct($data)
	{
		$this -> host = $data -> host;
		$this -> port = $data -> port;
		$this -> user = $data -> user;
		$this -> password = $data -> password;
		$this -> database = $data -> database;
		$this -> writable = $data -> writable; 
		
		$this -> connect();
	}
	
	abstract function connect();
	
	abstract function getDriver();
	
	abstract function createShardTable($tblName, $data);
	
	abstract function tableExists($tableName);
}