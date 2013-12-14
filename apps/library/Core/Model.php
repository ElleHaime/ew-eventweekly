<?php

namespace Core;

use Core\Utils as _U;

class Model extends \Phalcon\Mvc\Model
{
	public $extraOptions;


	public function onConstruct()
	{
		$di = $this -> getDi();
	}

	public function getDependencyProperty()
	{
		$relationsBelongs = $this -> getModelsManager() -> getBelongsTo($this);		
		$relationsManyToMany = $this -> getModelsManager() -> getHasManyToMany($this);

		if (!empty($relationsBelongs)) {
			foreach ($relationsBelongs as $i => $rel) {
				$refOptions = $rel -> getOptions();
				
				$alias = $this -> getRelationAlias($refOptions);
				$aliasName = $this -> getRelationAliasName($refOptions);
				
				if ($addons = $this -> getExtraRelations($alias)) {
					foreach ($addons as $field => $val) {
						$valName = $alias . '_' . $val;
						$this -> $valName = $this -> $alias -> $val;
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

				$alias = $this -> getRelationAlias($refOptions);
				$aliasName = $this -> getRelationAliasName($refOptions);
				
				$aliasList = [];
				foreach ($this -> $alias as $a) {
					$aliasList[$a -> id] = $a -> name;
				}
				$this -> $alias = null;
				$this -> $alias = $aliasList; 				
			}
		}

		return;
	}
	
	
	private function getRelationAlias($refOptions)
	{
		if (isset($refOptions['alias'])) {
			$alias = $refOptions['alias'];
		} else {
			$func = new \ReflectionClass($rel -> getReferencedModel());
			$alias = $func -> getShortName();
		}
		
		return $alias;;
	}
	
	private function getRelationAliasName($refOptions)
	{
		if (isset($refOptions['baseField'])) {
			$aliasName = $refOptions['baseField'];
		} else {
			$aliasName = 'name';
		}
		
		return $aliasName;
	}
	
	private function getExtraRelations($relName)
	{
		$extra = false;
		
		if (isset($this -> extraOptions[$relName])) {
			$extra = $this -> extraOptions[$relName];
		}
	
		return $extra;
	}


	public function setExtraRelations($addOptions = false)
	{
		$this -> extraOptions = $addOptions;

		return $this;
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
