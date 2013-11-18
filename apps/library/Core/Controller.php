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
	protected $locator				= false;
	
	
	public function initialize()
	{
		$this -> _setModule();
		$this -> _getChild();
		$this -> _parseQueryVals();
		
		if (!$this -> locator) {
			$this -> plugLocator();
		}

		if (!$this -> session -> has('location')) {
			$location = $this -> locator -> createOnChange();
			$this -> session -> set('location', $location);
		}

		if ($this -> session -> has('role') && $this -> session -> get('role') == Acl::ROLE_MEMBER) {
			$this -> memberId = $this -> session -> get('memberId');
			$this -> view -> member = $this -> session -> get('member');

			if ($this -> session -> has('user_token')) {
				$this -> view -> setVar('external_logged', 'facebook');
				if (isset($this -> view -> member -> network)) {
					$this -> view -> setVar('acc_external', $this -> view -> member -> network);
				}
			}
		} else {
			$this -> session -> set('role', Acl::ROLE_GUEST);
		}
		$this -> view -> setVar('location', $this -> session -> get('location'));	
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
	
	public function plugLocator()
	{
		$locModel = 'Location';
		$locPath = $this -> getModelPath() . $locModel;
		$this -> locator = new $locPath;
		$this -> locator -> setGeoService($this -> geo);
	}
}
