<?php

namespace Core;

use Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Phalcon\Acl as PhAcl,
	Phalcon\Events\Event,	
	Phalcon\Acl\Role as AclRole,
	Phalcon\Acl\Resource as AclResource,
	Phalcon\Acl\Adapter\Memory as AclMemory,
	Core\Acl\Roles as EwRoles,
	Core\Acl\Permissions as EwPermissions,
	Core\Acl\RolesPermissions as EwRolesPermitted;


class Acl extends Plugin
{
	const ROLE_ADMIN 	= 'admin';
	const ROLE_MEMBER	= 'member';
	const ROLE_GUEST	= 'guest';

	const ACL_CACHE 	= 'acl.cache';

	protected $_acl;
	protected $_roles 		= array(); 
	protected $_roleDefault = 'guest';

	public function __construct($dependencyInjector) 
	{
		$this -> _dependencyInjector = $dependencyInjector;
	}

	protected function _getAcl()
	{
		if (!$this -> _acl) {

			// search acl data in cache
			$cacheData = $this -> _dependencyInjector -> get('cacheData');
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

				$rolesPermitted = EwRolesPermitted::find();

				$permissions = EwPermissions::find();
				$resources = array();
				foreach ($permissions as $perm) {
					$rname = ucfirst($perm -> module) . ucfirst($perm -> controller);
					if (!isset($resources[$rname])) {
						$resources[$rname] = array();
					}
					$resources[$rname]['actions'][$perm -> id] = $perm -> action;
				}

				foreach ($resources as $resName => $resOptions) {
					$resource = new AclResource($resName);
					$acl -> addResource($resource, $resOptions['actions']);

					foreach ($rolesPermitted as $rp) {

						if (isset($resOptions['actions'][$rp -> permissions_id])) {
							$acl -> allow($rolesScope[$rp -> roles_id], 
										  $resName,
										  $resOptions['actions'][$rp -> permissions_id]);
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
				'action' => 'index'
			)); 

			return false;
		} 
	}


	public function clearAcl()
	{
		$this -> _dependencyInjector -> get('cacheData') -> delete(self::ACL_CACHE);
	}

}