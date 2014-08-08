<?php 

namespace Sharding\Core\Adapter\Mysql;

use Sharding\Core\Loader\Config,
	Sharding\Core\Adapter\AdapterAbstractFactory,
	Core\Utils as _U;

class MysqlFactory extends AdapterAbstractFactory
{
	function addConnection($data) 
	{
		return new \Sharding\Core\Adapter\Mysql\Mysql($data);
	}
} 