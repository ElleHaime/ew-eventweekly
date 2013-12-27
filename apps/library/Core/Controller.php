<?php

namespace Core;

use Phalcon\Filter,
	Core\Acl,
	Core\Utils as _U,
	Frontend\Form\SearchForm,
    Frontend\Models\Location;

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
		
		
		if (!$this -> session -> isStarted()) {
			$this -> session -> start();
		}
		
		if (!$this -> locator) {
			$this -> plugLocator();
		}
		$this -> plugSearch();

        $member = $this -> session -> get('member');
        $loc = $this -> session -> get('location');

		if (!$loc || !is_object($member) || ($loc->id != $member->location_id)) {
            if ($member) {
                $location = Location::findFirst('id = '.$member->location_id);
            }
            if (!isset($location)) {
                $location = $this -> locator -> createOnChange();
            }
			$this -> session -> set('location', $location);
		}

		if ($this -> session -> has('eventsTotal')) {
			$this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
		}

        if ($this->session->has('location_conflict')) {
			$this->view->setVar('location_conflict', $this->session->get('location_conflict'));
            $this->session->remove('location_conflict');
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

        $this -> view -> setVar('userEventsCreated', $this -> session -> get('userEventsCreated'));
        $this -> view -> setVar('userEventsLiked', $this -> session -> get('userEventsLiked'));
        $this -> view -> setVar('userEventsGoing', $this -> session -> get('userEventsGoing'));
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
	
	public function plugSearch()
	{
		$searchForm = new SearchForm();
		$this -> view -> setVar('searchForm', $searchForm);
	
		$categories = \Frontend\Models\Category::find();
		$categories = $categories -> toArray();
		$this -> view -> setVar('formCategories', $categories);
	}
	
	
	public function plugLocator()
	{
		$locModel = 'Location';
		$locPath = $this -> getModelPath() . $locModel;
		$this -> locator = new $locPath;
	}

    protected function setFlash($text = '', $type = 'info') {
        $this->session->set('flashMsgText', $text);
        $this->session->set('flashMsgType', $type);
        return $this;
    }

    protected function sendAjax($data)
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($data);
        $this->response->send();
    }
}
