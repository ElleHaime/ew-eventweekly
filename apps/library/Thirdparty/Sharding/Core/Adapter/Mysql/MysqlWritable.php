<?php 

namespace Sharding\Core\Adapter\Mysql;

use Sharding\Core\Adapter\AdapterAbstractWritable,
	Core\Utils as _U;

class MysqlWritable extends AdapterAbstractWritable
{
	use \Sharding\Core\Adapter\Mysql\TMysql;
	
	public function createShardTable($tblName, $data)
	{
		if ($this -> writable) {
			$query = str_replace('$tableName', $tblName, $data);
			/* validation, big heap of validations */
			/* and create this fucking table*/
			return;
		}
	}
} 