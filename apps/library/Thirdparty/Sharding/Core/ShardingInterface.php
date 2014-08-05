<?php 

namespace Sharding;

trait ShardingInterface
{
	abstract public function setReadDestinationDb();
	
	abstract public function setReadDestinationTable();
	
	abstract public function setWriteDestinationDb();
	
	abstract public function setWriteDestinationTable();
}