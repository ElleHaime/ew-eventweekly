<?php 

namespace Sharding\Core\Adapter\Mysql;

use Sharding\Core\Adapter\AdapterAbstractWritable,
	Core\Utils as _U;

class MysqlWritable extends AdapterAbstractWritable
{
	use \Sharding\Core\Adapter\Mysql\TMysql;
	
	public function createShardMap($tblName, $data)
	{
		if ($this -> writable) {
			$query = str_replace('$tableName', $tblName, $data);
			/* validation, big heap of validations */
			/* and create this fucking table*/
			try {
				$this -> connection -> query($query);
			} catch(\Exception $e) {
				throw new \Exception('Unable to create mapping table');
			}
			
			return;
		}
	}
	
	public function createTableBySample($tblName)
	{
		if ($this -> tableExists($tblName)) {
			return;
		}
		
		$structure = $this -> getTableStructure();

		if ($structure) {
			if (!empty($structure[0]['Create Table'])) {
				$query = str_replace("`" . $structure[0]['Table'] . "`", "`" . $tblName . "`", $structure[0]['Create Table']);
				try {
					$this -> connection -> query($query);
				} catch (\PDOException $e) {
					$this -> errors = $e -> getMessage();
				}
				/*$query = 'CREATE TABLE ' . $tblName . '(';
				
				foreach ($structure as $index => $data) {
					$field = $data['Field'] . ' ' . $data['Type'];
					
					if ($data['Null'] == 'NO') {
						$field .= ' NOT NULL';
					}
					 
					if ($data['Default'] === NULL) {
						if ($data['Null'] == 'YES') {
							$field .= ' default NULL';
						}
					} else {
						$field .= ' default ' . $data['Default'];
					}
					
					if ($data['Key'] == 'PRI') {
						$field .= ' primary key';				
					}
					
					$field .= ', ';
					
					$query .= $field;
				}
				
				$query = substr($query, 0, strlen($query)-2) . ')';
				
				_U::dump($query); */
			}
		}
		
		return;
	}
} 