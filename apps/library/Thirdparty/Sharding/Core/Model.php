<?php 

namespace Sharding\Core;

use Core\Utils as _U;

class Model
{
	public $app;
	public $entity;
	public $connection;
	
	private $fields;
	
	
	public function __construct($app)
	{
		$this -> app = $app;
	}
	
	public function getEntityStructure()
	{
		$structure = $this -> connection -> setTable($this -> entity)
										 -> getTableStructure();
		return $structure; 
	}
	
	public function save($data)
	{
		$result = $this -> connection -> setTable($this -> entity)
									  -> saveModel($data);
		if ($result) {
			$this -> id = $result;
			return $this;
		} else {
			return false;
		}
	}
	
	public function setConnection($conn)
	{
		$this -> connection = $this -> app -> connections -> $conn;
	}
	
	public function setEntity($entity)
	{
		$this -> entity = $entity;
	}
}