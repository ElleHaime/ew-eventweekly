<?php

namespace Core;

use Phalcon\Mvc\User\Plugin;


class Annotations extends Plugin
{
	protected $di;
	protected $config;
	protected $routes		= array();
	protected $permissions	= array();

	public function __construct($dependencyInjector) 
	{
		$this -> di = $dependencyInjector;
		$this -> config = $this -> di -> get('config');
	}

	public function run()
	{
		$reader = new \Phalcon\Annotations\Adapter\Memory();
		$modules = $this -> di -> get('modules');

		if ($modules) {
			foreach ($modules as $module => $settings) {
				if (!$this -> config -> modules -> $module -> enabled) {
					continue;
				}
				
				$cntPath = ucfirst($module) . '\Controllers';
				$controllers = scandir($settings -> namespaces -> $cntPath);
				
				foreach ($controllers as $item => $file) {
					if ($file == "." || $file == "..") {
						continue;
					}
				
					$controllerNS = $cntPath . '\\' . str_replace('.php', '', $file);
					$reflector = $reader -> get($controllerNS);

					$aclResourceName = ucfirst($module) . ucfirst(str_replace('.php', '', (str_replace('Controller', '', $file))));
					if (!isset($this -> permissions[$aclResourceName])) {
						$this -> permissions[$aclResourceName] = array();
					}
						
					$annotationsClass = $reflector -> getClassAnnotations();
					$annotationsMethods = $reflector -> getMethodsAnnotations();

					if ($annotationsMethods) {
						foreach ($annotationsMethods as $methodName => $docblock) {
							
							$aclAction = str_replace('Action', '', $methodName);
							if (!isset($this -> permissions[$aclResourceName][$aclAction])) {
								$this -> permissions[$aclResourceName][$aclAction] = array();
							}		
							
							foreach($docblock -> getAnnotations() as $object) {
								$arg = $object -> getArguments();
								
								switch ($object -> getName()) {
									case 'Route':
											$this -> routes[$arg[0]] = array('module' => $module,
																			 'controller' => strtolower(str_replace('Controller.php', '', $file)),
																			 'action' => str_replace('Action', '', $methodName));
										break;
									
									case 'Acl':
											if (isset($arg['roles']) && !empty($arg['roles'])) {
												foreach ($arg['roles'] as $i => $role) {
													$this -> permissions[$aclResourceName][$aclAction][$i] = $role; 
												} 
											}
										break; 
								}
							}//var_dump($this -> routes);die;
						}
					}
				}				
			}
		}
	}
	
	
	public function getPermissions()
	{
		return $this -> permissions;
	}
	
	
	public function getRoutes()
	{
		return $this -> routes;
	}
}