<?php

namespace Core;

use Phalcon\Filter,
    Core\Acl,
    Core\Utils as _U,
    Frontend\Form\SearchForm,
    Frontend\Models\Location,
    Frontend\Models\Event,
    Frontend\Models\Venue,
    Frontend\Models\Cron,
    Frontend\Models\EventMember,
    Frontend\Models\EventMemberFriend,
    Frontend\Models\MemberNetwork,
    Frontend\Models\EventCategory AS CountEvent,
    Thirdparty\MobileDetect\MobileDetect;

class Controller extends \Phalcon\Mvc\Controller
{
    protected $queryGetVals = array();
    protected $queryPostVals = array();
    protected $obj = false;
    protected $model = false;
    protected $module = false;
    protected $memberId = false;

    public $eventListCreatorFlag = false;


    public function initialize()
    {
//_U::dump($this->session->has('userSearch'));    	
//_U::dump($this->session->get('location'));    	
        $this -> _setModule();
        $this -> _getChild();
        $this -> _parseQueryVals(); 

        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        $this -> plugSearch();
        $this -> checkCache();
        $this -> counters -> setUserCounters();

        $member = $this->session->get('member');

        //$loc = $this->session->get('location');
        if ($this->session->get('location') === null) {
            $locModel = new Location();
            $loc = $locModel -> createOnChange();
            $this -> session -> set('location', $loc);
        }
        
        if ($this->session->has('role') && $this->session->get('role') == Acl::ROLE_MEMBER) {
        	$this->memberId = $this->session->get('memberId');
        	$this->view->member = $this->session->get('member');
        
        	if ($this->session->has('user_token')) {
        		$this->view->setVar('external_logged', 'facebook');
        
        		$this->view->setVar('permission_base', $this->session->get('permission_base'));
        		$this->view->setVar('permission_publish', $this->session->get('permission_publish'));
        		$this->view->setVar('permission_manage', $this->session->get('permission_manage'));
        
        		if (isset($this->view->member->network)) {
        			$this->view->setVar('acc_external', $this->view->member->network);
        		}
        	}
        
        	if (isset($member) && ($member->auth_type == 'email' && isset($member->network->account_uid))) {
        		$this->view->setVar('acc_external', $member->network);
        	}
        	 
        } else {
        	$this->session->set('role', Acl::ROLE_GUEST);
        }

        if ($this->session->has('location_conflict')) {
            $this->view->setVar('location_conflict', $this->session->get('location_conflict'));
            $this->session->remove('location_conflict');
        }
        $this->view->setVar('location', $this->session->get('location'));
///_U::dump($this->view->getVar('location')->toArray());        

        if ($this->session->has('acc_synced') && $this->session->get('acc_synced') !== false) {
            $this->view->setVar('acc_synced', 1);
        }

        $this->view->setVar('eventListCreatorFlag', $this->eventListCreatorFlag);
        
        $this -> filtersBuilder -> load();
        $this -> view -> setVar('userSearch', $this -> filtersBuilder -> getFormFilters());
//_U::dump($this -> filtersBuilder -> getFormFilters());
        $detect = new MobileDetect();
        if ($detect -> isMobile() || $detect -> isTablet()) {
            $this->view->setVar('isMobile', '1');
        } else {
            $this->view->setVar('isMobile', '0');
        }
        isset($this -> getDI() -> get('facebook_config') -> facebook -> version) ? $fbAppVersion = $this -> getDI() -> get('facebook_config') -> facebook -> version : $fbAppVersion = 'v2.0'; 
        $this -> view -> setVar('fbAppVersion', $this -> getDI() -> get('facebook_config') -> facebook -> version);
        
        (new Cron()) -> createUserTask();
    }


    public function getObj()
    {
        return $this->obj;
    }

    
    public function getModel()
    {
        return $this->model;
    }

    
    protected function _parseQueryVals()
    {
        foreach ($this->dispatcher->getParams() as $param => $value) {
            $this->queryGetVals[$param] = $value;
        }

        if ($this->request->isPost()) {
            foreach ($this->request->getPost() as $param => $val) {
                //$this -> queryPostParams[$param] = $val;
            }
        }
    }

    
    protected function  _setObj($obj)
    {
        $this->obj = $obj;
    }


    protected function _setModel($model)
    {
        $this->model = $model;
    }


    protected function _setModule()
    {
        $this->module = $this->dispatcher->getModuleName();
    }


    protected function _getChild()
    {
        $childClass = explode('\\', get_class($this));
        $chunkPosition = strpos($childClass[count($childClass) - 1], 'Controller');
        $modelName = substr($childClass[count($childClass) - 1], 0, $chunkPosition);
        $modelClass = $this->getModelPath() . $modelName;

        $this->_setModel($modelName);
        $this->_setObj($modelClass);
    }

    
    public function getModelPath()
    {
        $module = $this->module;

        return '\\' . $this->config->modules->$module->defaultNameSpace . '\Models\\';
    }

    
    public function getFormPath()
    {
        $module = $this->module;

        return '\\' . $this->config->modules->$module->formNamespace . '\\';
    }

    
    public function plugSearch()
    {
        $searchForm = new SearchForm();
        $this->view->setVar('searchForm', $searchForm);

        $categories = \Frontend\Models\Category::find();
        $categories = $categories->toArray();
        $this->view->setVar('formCategories', $categories);
    }

    
    protected function setFlash($text = '', $type = 'info')
    {
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
    
    
    public function setPermissions()
    {
    	if ($this->session->has('role') && $this->session->get('role') == Acl::ROLE_MEMBER) {
    		$this->memberId = $this->session->get('memberId');
    		$this->view->member = $this->session->get('member');
    	
    		if ($this->session->has('user_token')) {
    			$this->view->setVar('external_logged', 'facebook');
    	
    			$this->view->setVar('permission_base', $this->session->get('permission_base'));
    			$this->view->setVar('permission_publish', $this->session->get('permission_publish'));
    			$this->view->setVar('permission_manage', $this->session->get('permission_manage'));
    	
    			if (isset($this->view->member->network)) {
    				$this->view->setVar('acc_external', $this->view->member->network);
    			}
    		}
    	
    		if (isset($member) && ($member->auth_type == 'email' && isset($member->network->account_uid))) {
    			$this->view->setVar('acc_external', $member->network);
    		}
    		 
    	} else {
    		$this->session->set('role', Acl::ROLE_GUEST);
    	}
    }
    
    
    public function upSessionArray($sessName, $varName, $varValue)
    {
    	if ($this -> session -> has($sessName)) {
    		$var = $this -> session -> get($sessName);
    		$var[$varName] = $varValue;
    		$this -> session -> set($sessName, $var);
    		
    		return true;
    	} 
    	
    	return false;
    }
    

    public function checkCache()
    {
    	if (!$this->cacheData->exists('eventsGTotal')) {
    		$event = new Event();
    		$event -> setCacheTotal();
    	}
    }
}
