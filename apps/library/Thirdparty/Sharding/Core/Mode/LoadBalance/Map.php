<?php 

namespace Sharding\Core\Mode\Loadbalance;

use Core\Utils as _U,
	Sharding\Core\Loader as Loader;

class Map
{
	public $id;
	public $criteria;
	public $dbname;
	public $tblname;
	
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
		return $result; 
	}
	
	public function findAll()
	{
		
	}
	
	public function saveShard()
	{
		
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
}