<?php 

use Core\Model;

namespace Sharding\Core\Env\Converter;

use Core\Utils as _U,
    Sharding\Core\Loader as Loader,
	Sharding\Core\Model\Model as Model,
	Sharding\Core\Env\Helper\THelper as Helper;

	
trait Phalcon
{
	public $convertLoader;
	
	/**
	 * @Route("/sharding/appendstruct", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});
	 */
	public function appendStructureAction()
	{
		$this -> convertLoader = new Loader();
		
		$shardMapPrefix = $this -> convertLoader -> getMapPrefix();
		foreach ($this -> convertLoader -> config -> shardModels as $model => $data) {
			if ($data -> shards) {
				foreach ($data -> shards as $db => $shard) {
					foreach ($this -> convertLoader -> connections as $conn) {
						if (!$conn -> tableExists($shardMapPrefix . strtolower($model))) {
							$shardType = $data -> shardType;
							$driver = $conn -> getDriver();
							$conn -> createShardMap($shardMapPrefix . strtolower($model),
									$this -> convertLoader -> serviceConfig -> mode -> $shardType -> schema -> $driver);
						}
					}
				}
			}
		}
		
		
		$master = $this -> convertLoader -> getMasterConnection();
		$masterConn = $this -> convertLoader -> connections -> $master;
		
		foreach ($this -> convertLoader -> config -> shardModels as $model => $data) {
			if ($data -> shards) {
				foreach ($data -> shards as $db => $shard) {
					for($i = 1; $i <= $shard -> tablesMax; $i++) {
						$tblName = $shard -> baseTablePrefix . $i;
						$masterConn -> setTable($data -> baseTable) -> createTableBySample($tblName);
		
						if (isset($data -> relations)) {
							foreach ($data -> relations as $relation => $elem) {
								$tblRelName = $elem -> baseTablePrefix . $i;
								$masterConn -> setTable($elem -> baseTable) -> createTableBySample($tblRelName);
							}
						}
					}
				}
			}
		}		
_U::dump('ready');		
	}
	
	
	/**
     * @Route("/sharding/convert", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
	public function transferDataAction()
	{
		$this -> convertLoader = new Loader(); 

		foreach ($this -> convertLoader -> config -> shardModels as $object => $data) {
			$objName = $data -> namespace . '\\' . $object;
			$objPrimary = $data -> primary;
			$objCriteria = $data -> criteria;
			$obj = new $objName;
			$obj -> setConvertationMode();
			
			$items = $obj::find(['limit' => 2]);

			foreach ($items as $e) {
				$oldId = $e -> $objPrimary;
				if (is_null($e -> $objCriteria) or empty($e -> $objCriteria) or $e -> $objCriteria === false) {
					$e -> $objCriteria = 0;
				}
				
				$e -> setShardByCriteria($e -> $objCriteria);
				if ($newObj = $e -> save()) {
					$e -> $objPrimary = $newObj;
					
					$hasOneRelations = $e -> getModelsManager() -> getHasOne(new $objName);
					if (!empty($hasOneRelations)) {
						foreach ($hasOneRelations as $index => $rel) {
							$relOption = $rel -> getOptions();
							$relField = $rel -> getReferencedFields();
							$relations = $e -> $relOption['alias'];
								
							if ($relations) {
								foreach ($relations as $obj) {
									$obj -> $relField = $e -> $objPrimary;
									$obj -> update();
								}
							}
						}
					}
					
					$hasManyRelations = $e -> getModelsManager() -> getHasMany(new $objName);
					if (!empty($hasManyRelations)) {
						foreach ($hasManyRelations as $index => $rel) {
							$relOption = $rel -> getOptions();
							$relField = $rel -> getReferencedFields();
							$relations = $e -> $relOption['alias'];
					
							if ($relations) {
								foreach ($relations as $obj) {
									$obj -> $relField = $e -> $objPrimary;
									$obj -> update();
								}
							}
						}
					}
	
					$hasManyToManyRelations = $e -> getModelsManager() -> getHasManyToMany(new $objName);
					if (!empty($hasManyToManyRelations)) {
						foreach ($hasManyToManyRelations as $index => $rel) {
							$modelName = $rel -> getIntermediateModel();
							$defField = $rel -> getIntermediateFields(); 
		
							$interObject = $modelName::find([$defField . '=' . $oldId]);
							foreach($interObject as $obj) {
								$obj -> $defField = $e -> $objPrimary;
								$obj -> update();
							} 
						}
					}
				}	
			}		
		}
		
_U::dump('this is the end');		
	}
}