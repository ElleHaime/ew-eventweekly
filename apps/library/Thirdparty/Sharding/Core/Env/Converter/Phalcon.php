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
	public $shconfig;
	public $serviceConfig;
	
	
	public function onConstruct()
	{
		if (!$this -> shconfig = $this -> getDi() -> get('shardingConfig')) {
			throw new Exception('Sharding config not found');
			return false; 
		}
		if (!$this -> serviceConfig = $this -> getDi() -> get('shardingServiceConfig')) {
			throw new Exception('Sharding service config not found');
			return false; 
		}
	}
	
	/**
	 * @Route("/sharding/appendstruct", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});
	 */
	public function appendStructureAction()
	{
		$this -> convertLoader = new Loader($this -> shconfig, $this -> serviceConfig);
		
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
		die('ready');
	}
	
	
	/**
     * @Route("/sharding/conver", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
	public function transferDataAction()
	{
		$this -> convertLoader = new Loader($this -> shconfig, $this -> serviceConfig); 

		foreach ($this -> convertLoader -> config -> shardModels as $object => $data) {

			$objRelationScope = [];
			if ($data -> relations) {
				foreach ($data -> relations as $relName => $relData) {
					$objRelationScope[$relData -> namespace . '\\' . $relName] = $relData;
				}
			}
			$objFileScope = [];
			if (isset($data -> files)) {
				foreach ($data -> files as $relName => $relData) {
					$objFileScope[$relName] = $relData;
				}
			}
			
			$objName = $data -> namespace . '\\' . $object;
			$objTableName = $data -> baseTable;
			$objPrimary = $data -> primary;
			$objCriteria = $data -> criteria;
			
			
			$offset = 5000;
			$number = 1000;
			
				$obj = new $objName;
				$obj -> setConvertationMode();
				$items = $obj::find(['limit' => ['number' => $number, 'offset' => $offset]]);

				if ($items) {
					foreach ($items as $e) {
						$oldId = $e -> $objPrimary;
						
						if (is_null($e -> $objCriteria) or empty($e -> $objCriteria) or $e -> $objCriteria === false) {
							$e -> $objCriteria = 0;
						}
						
						$e -> setShardByCriteria($e -> $objCriteria);
					
						if ($newObj = $e -> save()) {
		
							if (!empty($objFileScope)) {
								foreach ($objFileScope as $fileRel => $fileData) {
									if (is_dir($fileData -> path . DIRECTORY_SEPARATOR . $oldId)) {
										$oldPathName = $fileData -> path . DIRECTORY_SEPARATOR . $oldId;
										$newPathName = str_replace(DIRECTORY_SEPARATOR . $oldId, DIRECTORY_SEPARATOR . $newObj -> id, $oldPathName);
		
										try {
											rename($oldPathName, $newPathName);
										} catch(\Exception $e) {
											echo('ooooooooooops, can\'t rename folder ' . $oldPathName . '<br>');									
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
											$obj -> $relField = $newObj -> id;
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
									
									if (array_key_exists($relModel, $objRelationScope)) {
										$dest = new $relModel;
										$dest -> setConvertationMode();
																				
										$relations = $dest::find($relField . ' = "' . $oldId . '"');
										if ($relations) {
											foreach ($relations as $relObj) {
												$relObj -> $relField = $newObj -> id;
												$relObj -> setConvertationMode(false);
												//$relObj -> setShardByParentId($newObj -> id, $objRelationScope[$relModel]);
												$relObj -> setShardById($newObj -> id);
												$relObj -> save();
											}
										}
									} else {
										$relations = $e -> $relOption['alias'];
										if ($relations) {
											foreach ($relations as $obj) {
												$obj -> $relField = $newObj -> id;
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
		
									if (array_key_exists($relModel, $objRelationScope)) {
										$dest = new $relModel;
										$dest -> setConvertationMode();
		
										$relations = $dest::find($relField . ' = "' . $oldId . '"');
										if ($relations) {
											foreach ($relations as $relObj) {
												$relObj -> $relField = $newObj -> id;
												$relObj -> setConvertationMode(false);
												//$relObj -> setShardByParentId($newObj -> id, $objRelationScope[$relModel]);
												$relObj -> setShardById($newObj -> id);
												$relObj -> save();
											}
										}
									} else {
										$relations = $relModel::find($relField . ' = ' . $oldId);
										if ($relations) {
											foreach ($relations as $obj) {
												$obj -> $relField = $newObj -> id;
												$obj -> update();
											}
										}
									}
								}
							}
						}
					}
				} else {
					_U::dump('no items', true);				
				} 
		}
		
		die('this is the end');		
	}
}