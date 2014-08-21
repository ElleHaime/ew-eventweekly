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
	
	
	public function getTableStructure()
	{
		$structure = false;
		
		if ($this -> queryTable) {
			$query = 'DESCRIBE ' . $this -> queryTable;
			$structure = $this -> connection -> query($query) -> fetchAll(\PDO::FETCH_ASSOC);
		} 

		return $structure;
	}
	
	
	public function getDriver()
	{
		return 'mysql';
	}
	
	public function setTable($table)
	{
		$this -> queryTable = $table;
		return $this;
	}
	
	public function addCondition($condition)
	{
		$this -> conditions[] = $condition;
		return $this; 
	}
	
	public function addField($field)
	{
		$this -> fields[] = $field;
	} 
	
	public function addLimit($limit)
	{
		$this -> limit = $limit;
	}
	
	public function fetchOne()
	{
		$this -> queryExpr = 'SELECT ';
		$this -> processFields();		
		$this -> queryExpr .= ' FROM ' . $this -> queryTable;
		$this -> processConditions();
		
		$fetch = $this -> connection -> query($this -> queryExpr);
		if ($fetch  -> rowCount() == 0) {
			$result = false;
		} else {
			if ($this -> fetchFormat == 'OBJECT') {
				$result = $fetch -> fetch(\PDO::FETCH_LAZY);
			} else {
				$result = $fetch -> fetch(\PDO::FETCH_ASSOC);
			}
		}
		
		$this -> clearQuery();
		
		return $result;
	}
	
	public function fetch()
	{
		
	}
	

	public function execute($query)
	{
		try {
			$result = $this -> connection -> query($query);
			return $result;			
		} catch(\Exception $e) {
			throw new \Exception('Unable to create mapping table');
		}
	}
	
	private function clearQuery()
	{
		$this -> queryTable = false;
		$this -> limit = false;
		$this -> offset = false;
		$this -> fields = [];
		$this -> conditions = [];
		$this -> queryExpr = '';
		
		return;
	}
	
	private function processFields()
	{
		if (!empty($this -> fields)) {
			$this -> processFields();
			foreach ($this -> fields as $index => $field) {
				$this -> queryExpr .= $this -> queryTable . '.' . $field . ',';
			}
			$this -> queryExpr = substr($this -> queryExpr, 0, strlen($this -> queryExpr) - 1);
		} else {
			$this -> queryExpr .= '*';
		}
		
		return;
	}
	
	private function processConditions()
	{
		if (!empty($this -> conditions)) {
			$this -> queryExpr .= ' WHERE ';
			$conds = count($this -> conditions);
				
			for ($i = 0; $i < $conds; $i++) {
				$this -> queryExpr .= $this -> conditions[$i] . ' ';
				if ($i < $conds - 1) {
					$this -> queryExpr .= 'AND ';
				}
			}
		}
		
		return;
	}
}