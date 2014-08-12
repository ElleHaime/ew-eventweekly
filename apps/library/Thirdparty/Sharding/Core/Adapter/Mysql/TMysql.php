<?php 

namespace Sharding\Core\Adapter\Mysql;

use Core\Utils as _U;

trait TMysql
{
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