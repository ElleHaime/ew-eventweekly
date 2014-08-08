<?php 

namespace Sharding\Core\Adapter\Mysql;

use Sharding\Core\Adapter\AdapterAbstract,
	Core\Utils as _U;

class Mysql extends AdapterAbstract
{
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
		$this -> writeable = $data -> writeable; 
		
		$this -> connect();
	}
	
	public function connect()
	{
		try {
			$this -> connection = new \PDO('mysql:host=' . $this -> host . ';port=' . $this -> port . ';dbname=' . $this -> database . ';charset=utf8', $this -> user, $this -> password);
			$this -> connection -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch(\PDOException $e) {
			$this -> errors = $e -> getMessage();
		}
		
		return $this;
	}
	
	public function createTable($tblName, $data)
	{
		if ($this -> writeable) {
			_U::dump($tblName, true);
			_U::dump($data);
		}
	}
	
	public function tableExists($tblName)
	{
		$query = 'SELECT table_name FROM information_schema.tables WHERE table_schema = "' . $this -> database . '" AND table_name = "' . $tblName . '"';
		$tblExists = $this -> connection -> query($query) -> rowCount();
		
		if ($tblExists != 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getDriver()
	{
		return 'mysql';
	}
} 