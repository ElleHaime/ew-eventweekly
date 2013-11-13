<?php

namespace Core;

use Phalcon\Filter,
	Core\Acl,
	Core\Utils as _U;

class Controller extends \Phalcon\Mvc\Controller
{
	protected $queryGetVals 		= array();
	protected $queryPostVals		= array();
	protected $obj					= false;
	protected $model				= false;
	protected $module 				= false;
	protected $memberId				= false;
	
 	public function onConstruct()
	{
		$this -> _setModule();
		$this -> _getChild();
		$this -> _parseQueryVals();
		
		$location = $this -> geo -> getUserLocation(array('city', 'country'));

		if ($this -> session -> has('role') && $this -> session -> get('role') == Acl::ROLE_MEMBER) {
			$this -> memberId = $this -> session -> get('memberId');
			$this -> view -> member = $this -> session -> get('member');

			if (isset($this -> view -> member -> location)) {
				$this -> view -> setVar('location', $this -> view -> member -> location -> name);
				$this -> session -> set('location', $this -> view -> member -> location -> name);			
			} else {
				$this -> view -> setVar('location', $location);
				$this -> session -> set('location', $location);	
			}
		} else {
			$this -> session -> set('role', Acl::ROLE_GUEST);
			$this -> session -> set('location', $location);	
			$this -> view -> setVar('location', $this -> geo -> getUserLocation(array('city')));
		}
	}

	public function getObj()
	{
		return $this -> obj;
	}

	public function getModel()
	{
		return $this -> model;
	}

	protected function _parseQueryVals()
	{
		foreach($this -> dispatcher -> getParams() as $param => $value) {
			$this -> queryGetVals[$param] = $value;
		}

		if ($this -> request -> isPost()) {
			foreach ($this -> request -> getPost() as $param => $val) {
				//$this -> queryPostParams[$param] = $val;
			}
		}
	}

	protected function  _setObj($obj)
	{
		$this -> obj = $obj;
	}


	protected function _setModel($model) 
	{
		$this -> model = $model;
	}


	protected function _setModule()
	{
		$this -> module = $this -> dispatcher -> getModuleName();
	}
	

	protected function _getChild()
	{
		$childClass = explode('\\', get_class($this));
		$chunkPosition = strpos($childClass[count($childClass) - 1], 'Controller');
		$modelName = substr($childClass[count($childClass) - 1], 0, $chunkPosition);
		$modelClass =  $this -> getModelPath() . $modelName; 

		$this -> _setModel($modelName);
		$this -> _setObj($modelClass);
	}

	public function getModelPath()
	{
		$module = $this -> module;

		return '\\' . $this -> config -> modules -> $module -> defaultNameSpace . '\Models\\';
	}

	public function getFormPath()
	{
		$module = $this -> module;
		
		return '\\' . $this -> config -> modules -> $module -> formNamespace . '\\';
	}
}
