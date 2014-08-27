<?php 

namespace Sharding\Core\Mode\Loadbalance;

use Core\Utils as _U;

class Map
{
	public $id			= false;
	public $criteria	= false;
	public $dbname		= false;
	public $tblname		= false;
	
	public $entity;
	public $connection;
	public $app;
	
	
	public function __construct($app)
	{
		$this -> app = $app;
	}
	
	/**
	 * Search shard by criteria in map table
	 * 
	 * @access public
	 * @param int|string $criteria
	 * @return PDO object | false 
	 */
	public function findByCriteria($criteria)
	{
		$result = $this -> connection -> setTable($this -> entity)
									  -> addCondition($this -> entity . '.criteria = ' . $criteria)
									  -> fetchOne();
		
		if ($result) {
			$this -> id = $result -> id;
			$this -> dbname = $result -> dbname;
			$this -> tblname = $result -> tblname;
			$this -> criteria = $result -> criteria;
		} 
		
		return;
	}
	
	public function findAll()
	{
		
	}
	
	public function save()
	{
		$data = ['criteria' => $this -> criteria,
				 'dbname' => $this -> dbname,
				 'tblname' => $this -> tblname];

		$result = $this -> connection -> setTable($this -> entity)
									  -> saveRecord($data);
		if ($result) {
			$this -> id = $result;
			return $this;
		} else {
			return false;
		}
	}
	
	public function useConnection($conn)
	{
		$this -> connection = $this -> app -> connections -> $conn;
	}
	
	public function setEntity($entity)
	{
		$prefix = $this -> app -> getMapPrefix();
		$this -> entity = $prefix . $entity;
	}
	
	public function setCriteria($criteria)
	{
		$this -> criteria = (int)$criteria;
	}
}