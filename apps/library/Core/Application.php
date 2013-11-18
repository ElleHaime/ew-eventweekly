<?php

use \Phalcon\Config;
use \Phalcon\DI\FactoryDefault as DIFactory;
use \Phalcon\Db\Adapter\Pdo\Mysql;
use \Phalcon\Mvc\Application as BaseApplication;
use \Phalcon\Mvc\Router;
use \Phalcon\Loader;
use \Phalcon\Mvc\Url;


class Application extends BaseApplication
{
	private $_config 			= null;
	private $_router 			= null;	
	protected $_loader			= null;
	public static $defModule	= 'frontend';
	public static $defNamespace	= '';
	
	
	public function __construct()
	{
		include_once(CONFIG_SOURCE);
		$this -> _config = new Config($cfg_settings); 
		
		$di = new DIFactory();
		$di -> setShared('config', $this -> _config);
		
		if ($this -> _config -> application -> defaultModule) {
			self::$defModule = $this -> _config -> application -> defaultModule;
		}
		if ($this -> _config -> application -> defaultNamespace) {
			self::$defNamespace = $this -> _config -> application -> defaultNamespace;
		}
		
		parent::__construct($di);
	}
	

	public function run()
	{
		$di = $this -> _dependencyInjector;
		
		$this -> _initModules($di);
		$this -> _initLoader($di);
		$this -> _initDatabase($di);		
		$this -> _initRouter($di);
		$this -> _initUrl($di);
		$this -> _initCache($di);
		$this -> _initModules($di);

		$di -> setShared('app', $this);
	}
	
	public function getOutput()
	{
		return $this -> handle() -> getContent();
	}

	protected function _initLoader(\Phalcon\DI $di) {
		$this -> _loader = new Loader();

		$namespaces = array();

		$appNamespaces = $this -> _config -> application -> namespaces;
		if ($appNamespaces) {
			foreach ($appNamespaces as $ns => $npath) {
				$namespaces[$ns] = $npath;
			}
		}

		$modules = $di -> get('modules');
		foreach($modules as $module) {
			if ($module -> namespaces) {
				foreach ($module -> namespaces as $ns => $npath) {
					$namespaces[$ns] = $npath;
				}
			} 			
		}
		$this -> _initNamespaces($namespaces);

		$this -> _loader -> register();
		$di -> set('loader', $this -> _loader);

	}

	protected function _initModules(\Phalcon\DI $di)
	{
		$modules = $this -> _config -> get('modules');

		if ($modules) {
			$di -> set('modules', 
				function() use ($modules) {
					return (array)$modules;
				}
			);

			$enabled = array();
			foreach($modules as $module) {
				$enabled[$module -> name] = array (
							'className' => $module -> bootstrapNs,
							'path' => $module -> bootstrapPath,
				);
			}
			$this -> registerModules($enabled);
		}
	}
	
// TODO: select routing type, straight or annotations	
	protected function _initRouter(\Phalcon\DI $di)
	{
		$this -> _router = new Router();
		
		$router = $this -> _config -> get('router');
		if ($router) {
			foreach($router as $rt_path => $rt_settings) {
				$this -> _router -> add($rt_path, (array)$rt_settings);
			}
			$this -> _router -> notFound(array(
									'module' => self::$defModule,
									'controller' => 'index',
									'action' => 'index'
			));  
			$this -> _router -> setDefaultModule($this -> _config -> application -> defaultModule);
			$this -> _router -> setDefaultNamespace($this -> _config -> application -> defaultNamespace);
			$this -> _router -> setDefaultController($this -> _config -> application -> defaultController);
			$this -> _router -> setDefaultAction($this -> _config -> application -> defaultAction);
		}

		$di -> set('router', $this -> _router);
	}
	

	protected function _initNamespaces($namespaces)
	{
		$this -> _loader -> registerNamespaces($namespaces);
	}
	
	protected function _initUrl(\Phalcon\DI $di)
	{
		$bu = $this -> _config -> application -> baseUri ? $this -> _config -> application -> baseUri : '/';
		
		$di -> set('url', 
			function() use ($bu) {
				$url = new Url();
				$url -> setBaseUri($bu);
				
				return $url;
			}
		);
	}

	
	protected function _initDatabase(\Phalcon\DI $di)
	{
		$adapter = '\Phalcon\Db\Adapter\Pdo\\' . $this -> _config -> database -> adapter;
		$config = $this -> _config;
		
		$di -> set('db',
			function () use ($config, $adapter) {
				$connection = new $adapter(
					array('host' => $config -> database -> host,
						  'username' => $config -> database -> username,
						  'password' => $config -> database -> password, 
						  'dbname' => $config -> database -> dbname
					)
				);

				return $connection;
			} 
		);
	}

	protected function _initCache(\Phalcon\DI $di)
	{
		/*if (!$this -> _config -> application -> debug) {

            // Get the parameters
			$cacheAdapter = '\Phalcon\Cache\Backend\\' . $this -> _config -> application -> cache -> adapter;
			$frontEndOptions = array('lifetime' => $this -> _config -> application -> cache -> lifetime);
			$backEndOptions = $this -> _config -> application -> cache -> toArray();
			$frontOutputCache = new \Phalcon\Cache\Frontend\Output($frontEndOptions);
			$frontDataCache = new \Phalcon\Cache\Frontend\Data($frontEndOptions);

			// Cache:View
			$viewCache = new $cacheAdapter($frontOutputCache, $backEndOptions);
			$di -> set('viewCache', $viewCache, false);

			// Cache:Output
			$scacheOutput = new $cacheAdapter($frontOutputCache, $backEndOptions);
			//$di -> set('cacheOutput', $cacheOutput, true);

			// Cache:Data
			$cacheData = new $cacheAdapter($frontDataCache, $backEndOptions);
			$di -> set('cacheData', $cacheData, true);

			// Cache:Models
			$cacheModels = new $cacheAdapter($frontDataCache, $backEndOptions);
			$di -> set('modelsCache', $cacheModels, true);

		} else { */
			$cache = new \Core\Cache\Mock(null);
			$di -> set('cacheData', $cache);
			$di -> set('cacheOutput', $cache);
			$di -> set('modelsCache', $cache);
			$di -> set('viewCache', $cache);
		//}
	}
}
