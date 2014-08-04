<?php 

namespace Sharding;

trait ShardingInterface
{
	abstract public function setDesctinationDb();
	
	abstract public function setDesctinationTable();
	
	abstract public function fetch();
	
	abstract public function fetchOne();
	
	abstract public function insertRecord();
	
	abstract public function updateRecord();
	
	abstract public function deleteRecord();
}