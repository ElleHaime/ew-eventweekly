<?php 

namespace Sharding\Core\Mode;

interface StrategyInterface
{
	public abstract function getDatabase();
	
	public abstract function getTable();
}