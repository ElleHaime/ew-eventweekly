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
     * @Route("/sharding/convert", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
	public function transferDataAction()
	{
		$this -> convertLoader = new Loader(); 
		//_U::dump($this -> convertLoader -> config -> shardModels);
		
		foreach ($this -> convertLoader -> config -> shardModels as $object => $data) {
			$objName = $data -> namespace . $object;
			$objPrimary = $data -> primary;
			$objCriteria = $data -> criteria;
			$obj = new $objName;
			$obj -> unsetNeedShard();
			
			$items = $obj::find(['limit' => 2]);
			
			foreach ($items as $e) {
				$oldId = $e -> $objPrimary;
				if (is_null($e -> $objCriteria) or empty($e -> $objCriteria) or $e -> $objCriteria === false) {
					$e -> $objCriteria = 0;
				}
				
				$e -> setShardByCriteria($e -> $objCriteria);
				if ($newObj = $e -> save()) {
					$e -> $objPrimary = $newObj;
	
					$hasManyToManyRelations = $e -> getModelsManager() -> getHasManyToMany(new $objName);
					if ($hasManyToManyRelations) {
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
					
					$hasManyRelations = $e -> getModelsManager() -> getHasMany(new $objName);
					if ($hasManyRelations) {
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
				}	
			}		
		}
	}
}