<?php 

namespace Sharding\Core\Adapter\Mysql;

use Sharding\Core\Adapter\AdapterAbstractFactory,
	Core\Utils as _U;

class MysqlFactory extends AdapterAbstractFactory
{
	function addConnection($data) 
	{
		if ($data -> writable) {
			return new \Sharding\Core\Adapter\Mysql\MysqlWritable($data);
		} else {
			return new \Sharding\Core\Adapter\Mysql\MysqlReadonly($data);
		}
	}
} 