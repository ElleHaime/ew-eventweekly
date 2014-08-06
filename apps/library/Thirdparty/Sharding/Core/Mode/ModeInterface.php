<?php 

namespace Sharding\Mode;

interface ModeInterface
{
	public abstract function getDatabase();
	
	public abstract function getTable();
}