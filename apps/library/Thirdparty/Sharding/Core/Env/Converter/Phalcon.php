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
     * @Route("/sharding/conver", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
	public function transferDataAction()
	{
		$this -> convertLoader = new Loader(); 

		foreach ($this -> convertLoader -> config -> shardModels as $object => $data) {
			
			$objRelationScope = [];
			if ($data -> relations) {
				foreach ($data -> relations as $relName => $relData) {
					$objRelationScope[$relData -> namespace . '\\' . $relName] = $relData;
				}
			}
			$objFileScope = [];
			if ($data -> files) {
				foreach ($data -> files as $relName => $relData) {
					$objFileScope[$relName] = $relData;
				}
			}
			
			$objName = $data -> namespace . '\\' . $object;
			$objPrimary = $data -> primary;
			$objCriteria = $data -> criteria;
			$obj = new $objName;
			
			$obj -> setConvertationMode();
			$items = $obj::find(['limit' => ['number' => 4, 'offset' => 45]]);

			foreach ($items as $e) {
				$oldId = $e -> $objPrimary;
				
				if (is_null($e -> $objCriteria) or empty($e -> $objCriteria) or $e -> $objCriteria === false) {
					$e -> $objCriteria = 0;
				}
				
				$e -> setShardByCriteria($e -> $objCriteria);
			
				if ($newObj = $e -> save()) {
_U::dump('oldId: ' . $oldId, true);					
_U::dump('newId: ' . $newObj, true);
_U::dump('locationId: ' . $e -> location_id, true);
_U::dump('event table: ' . $e -> destinationTable, true);

					if (!empty($objFileScope)) {
						foreach ($objFileScope as $fileRel => $fileData) {
_U::dump('old images: ' . $fileData -> path . DIRECTORY_SEPARATOR . $oldId, true);							
							if (is_dir($fileData -> path . DIRECTORY_SEPARATOR . $oldId)) {
								$oldPathName = $fileData -> path . DIRECTORY_SEPARATOR . $oldId;
								$newPathName = str_replace(DIRECTORY_SEPARATOR . $oldId, DIRECTORY_SEPARATOR . $newObj, $oldPathName);
_U::dump('new images: ' . $newPathName, true);								
								try {
									rename($oldPathName, $newPathName);
								} catch(\Exception $e) {
									_U::dump('ooooooooooops, can\'t rename folder', true);									
								}
							}
						}
					} 
					
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
_U::dump('table on find: ' . $dest -> destinationTable, true);						
										
								$relations = $dest::find($relField . ' = "' . $e -> $objPrimary . '"');
_U::dump('relations: ' . $relations -> count(), true);
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
_U::dump($relField . ' = "' . $e -> $objPrimary . '"', true);								
								$relations = $dest::find($relField . ' = "' . $e -> $objPrimary . '"');
_U::dump('relations: ' . $relations -> count(), true);							
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
echo('<br><br><br>');					
			}		
		}
		
_U::dump('this is the end');		
	}
}