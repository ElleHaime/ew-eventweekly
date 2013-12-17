<?php

namespace Core;

use Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Phalcon\Acl as PhAcl,
	Phalcon\Events\Event,	
	Phalcon\Acl\Role as AclRole,
	Phalcon\Acl\Resource as AclResource,
	Phalcon\Acl\Adapter\Memory as AclMemory,
	Core\Acl\Roles as EwRoles;


class Acl extends Plugin
{
	const ROLE_ADMIN 	= 'admin';
	const ROLE_MEMBER	= 'member';
	const ROLE_GUEST	= 'guest';

	const ACL_CACHE 	= 'acl.cache';

	protected $_acl;
	protected $_roles 		= array(); 
	protected $_roleDefault = 'guest';
	protected $di;
	protected $annotations;

	public function __construct($dependencyInjector) 
	{
		$this -> di = $dependencyInjector;
		$this -> annotations = $this -> di -> get('annotations');
	}

	protected function _getAcl()
	{
		if (!$this -> _acl) {

			// search acl data in cache
			$cacheData = $this -> di -> get('cacheData');
			$aclCache = $cacheData -> get(self::ACL_CACHE);

			if ($aclCache === null) {

				// create new permissions scope
				$acl = new AclMemory();
				$acl -> setDefaultAction(PhAcl::DENY);

				$this -> _roles = EwRoles::find();
				$rolesScope = array();
				foreach ($this -> _roles as $role) {
					$rolesScope[$role -> id] = $role -> type;
					if ($role -> is_default == 1) {
						$this -> _roleDefault = $role -> type;
					}
					if ($role -> extends) {
						$acl -> addRole($role -> type, $role -> extends);
					} else {
						$acl -> addRole($role -> type);
					}
				}

				$permissions = $this -> annotations -> getPermissions();

				if (!empty($permissions)) {
					foreach($permissions as $controller => $access) {
						$actions = array();
						$permitted = array();

						foreach ($access as $acc => $roles) {
							$actions[] = $acc;
							$permitted[$acc] = $roles;
						}			

						$resource = new AclResource($controller);
						$acl -> addResource($resource, $actions); 	

						foreach ($permitted as $action => $val) {
							foreach ($val as $i => $rl) {
								$acl -> allow($rl, $controller, $action);
							}
						}	
					}
				}

				$cacheData -> save(self::ACL_CACHE, $acl, 2592000);
				$this -> _acl = $acl;

			} else {
				$this -> _acl = unserialize($aclCache);
			}
		}
		
		return $this -> _acl;
	}

	
	public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
	{
		$role = $this -> _roleDefault;
		$this -> _getAcl();
		
		if ($this -> session -> has('member') && $this -> session -> has('role')) {
			$role = $this -> session -> get('role');
		} 

		$module = ucfirst(strtolower($dispatcher -> getModulename()));
		$controller = ucfirst(strtolower($dispatcher -> getControllerName()));
		$resource = $module . $controller;
		
		$allowed = $this -> _acl -> isAllowed($role, $resource, $dispatcher -> getActionName());
		
		if ($allowed != PhAcl::ALLOW) {
			$dispatcher -> forward(array(
				'module' => $dispatcher -> getModuleName(),
				'controller' => 'index',
				'action' => 'denied'
			)); 

			return false;
		} 
	}


	public function clearAcl()
	{
		$this -> di -> get('cacheData') -> delete(self::ACL_CACHE);
	}

}