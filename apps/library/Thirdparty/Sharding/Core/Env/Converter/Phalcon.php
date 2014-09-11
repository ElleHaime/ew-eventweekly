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
							$mapperName = $shardMapPrefix . strtolower($model);

							$conn -> createShardMap($mapperName,
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
			
			$relationScope = [];
			if ($data -> relations) {
				foreach ($data -> relations as $relName => $relData) {
					$objRelationScope[$relData -> namespace . '\\' . $relName] = $relData;
				}
			}
			
			$objName = $data -> namespace . '\\' . $object;
			$objPrimary = $data -> primary;
			$objCriteria = $data -> criteria;
			$obj = new $objName;
			
			$obj -> setConvertationMode();
			$items = $obj::find(['limit' => ['number' => 2, 'offset' => 6]]);
//_U::dump($items -> toArray());			
			foreach ($items as $e) {
				$oldId = $e -> $objPrimary;
_U::dump($e -> id, true);
_U::dump($e -> location_id, true);				
				if (is_null($e -> $objCriteria) or empty($e -> $objCriteria) or $e -> $objCriteria === false) {
					$e -> $objCriteria = 0;
				}
				
				$e -> setShardByCriteria($e -> $objCriteria);
				if ($newObj = $e -> save()) {
_U::dump('newId: ' . $newObj, true);					
					$hasOneRelations = $e -> getModelsManager() -> getHasOne(new $objName);
					if (!empty($hasOneRelations)) {
						foreach ($hasOneRelations as $index => $rel) {
							$relOption = $rel -> getOptions();
							$relField = $rel -> getReferencedFields();
							$relations = $e -> $relOption['alias'];
								
							if ($relations) {
								foreach ($relations as $obj) {
									$obj -> $relField = $newObj;
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
							$relModel = $rel -> getReferencedModel();
_U::dump($relModel, true);							
							if (array_key_exists($relModel, $objRelationScope)) {
								$dest = new $relModel;
								$dest -> setConvertationMode();
								$relations = $dest::find($relField . ' = ' . $e -> $objPrimary);

								if ($relations) {
									foreach ($relations as $relObj) {
										$relObj -> $relField = $newObj;
										$relObj -> setConvertationMode(false);
										$relObj -> setShardByParentId($newObj, $objRelationScope[$relModel]);
_U::dump($relObj -> destinationDb, true);
_U::dump($relObj -> destinationTable, true);
_U::dump($relObj -> toArray(), true);										
										
										$relObj -> save();
									}
								}
							} else {
_U::dump('not shardable', true);								
								$relations = $e -> $relOption['alias'];
								if ($relations) {
									foreach ($relations as $obj) {
										$obj -> $relField = $newObj;
										$obj -> update();
									}
								}
							}
						}
					} 
	
					$hasManyToManyRelations = $e -> getModelsManager() -> getHasManyToMany(new $objName);
					if (!empty($hasManyToManyRelations)) {
						foreach ($hasManyToManyRelations as $index => $rel) {
							$relOption = $rel -> getOptions();
							$relModel = $rel -> getIntermediateModel();
							$relField = $rel -> getIntermediateFields(); 
_U::dump($relModel, true);
							if (array_key_exists($relModel, $objRelationScope)) {
								$dest = new $relModel;
								$dest -> setConvertationMode();
								$relations = $dest::find($relField . ' = ' . $e -> $objPrimary);
							
								if ($relations) {
									foreach ($relations as $relObj) {
										$relObj -> $relField = $newObj;
										$relObj -> setConvertationMode(false);
										$relObj -> setShardByParentId($newObj, $objRelationScope[$relModel]);
_U::dump($relObj -> destinationDb, true);
_U::dump($relObj -> destinationTable, true);
_U::dump($relObj -> toArray(), true);
										$relObj -> save();
									}
								}
							} else {
_U::dump('not shardable', true);								
								$relations = $relModel::find($relField . ' = ' . $e -> $objPrimary);
								if ($relations) {
									foreach ($relations as $obj) {
										$obj -> $relField = $newObj;
										$obj -> update();
									}
								}
							}
						}
					}
				}	
			}		
		}
		
_U::dump('this is the end');		
	}
}