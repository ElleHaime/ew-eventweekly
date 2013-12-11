<?php

namespace Core;

use Core\Utils as _U;

class Model extends \Phalcon\Mvc\Model
{
	public function onConstruct()
	{
		$di = $this -> getDi();
	}

	public function getDependency()
	{
		$relationsBelongs = $this -> getModelsManager() -> getBelongsTo($this);		
		$relationsManyToMany = $this -> getModelsManager() -> getHasManyToMany($this);

		if (!empty($relationsBelongs)) {
			foreach ($relationsBelongs as $i => $rel) {
				$refOptions = $rel -> getOptions();
				
				$alias = $this -> _getRelationAlias($refOptions);
				$aliasName = $this -> _getRelationAliasName($refOptions);
				
				if ($addons = $this -> _getRelationAdditional($refOptions)) {
					foreach ($addons as $field => $val) {
						$this -> $val = $this -> $alias -> $val;
					}
				}
				$this -> $alias = $this -> $alias -> $aliasName;
			}
		}
		
		if (!empty($relationsManyToMany)) {
			foreach ($relationsManyToMany as $i => $rel) {
/*				_U::dump($rel -> getReferencedModel(), true);
				_U::dump($rel -> getReferencedFields(), true);
				_U::dump($rel -> getFields(), true);
				_U::dump($rel -> getIntermediateFields(), true);
				_U::dump($rel -> getIntermediateReferencedFields(), true); */
				$refOptions = $rel -> getOptions();

				$alias = $this -> _getRelationAlias($refOptions);
				$aliasName = $this -> _getRelationAliasName($refOptions);
				
				$aliasList = [];
				foreach ($this -> $alias as $a) {
					$aliasList[$a -> id] = $a -> name;
				}
				$this -> $alias = null;
				$this -> $alias = $aliasList; 				
			}
		}
//die();		
		return;
	}
	
	
	private function _getRelationAlias($refOptions)
	{
		if (isset($refOptions['alias'])) {
			$alias = $refOptions['alias'];
		} else {
			$func = new \ReflectionClass($rel -> getReferencedModel());
			$alias = $func -> getShortName();
		}
		
		return $alias;;
	}
	
	private function _getRelationAliasName($refOptions)
	{
		if (isset($refOptions['baseField'])) {
			$aliasName = $refOptions['baseField'];
		} else {
			$aliasName = 'name';
		}
		
		return $aliasName;
	}
	
	private function _getRelationAdditional($refOptions)
	{
		$additional = false;
		
		if (isset($refOptions['additionalFields'])) {
			$additional = $refOptions['additionalFields'];
		}
	
		return $additional;
	}
	
	public function createOnChange($argument)
	{
		return false;
	}
	
	protected function getConfig()
	{
		$config = $this -> getDi() -> get('config');
		return  $config;
	}
	
	protected function getGeo()
	{
		$geo = $this -> getDi() -> get('geo');
		return  $geo;
	}
}
