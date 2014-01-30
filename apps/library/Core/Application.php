<?php

use \Phalcon\Config;
use \Phalcon\DI\FactoryDefault as DIFactory;
use \Phalcon\Db\Adapter\Pdo\Mysql;
use \Phalcon\Mvc\Application as BaseApplication;
use \Phalcon\Mvc\Router;
use \Phalcon\Loader;
use \Phalcon\Mvc\Url;
use \Phalcon\Logger;
use \Phalcon\Events\Manager as EventsManager;
use \Phalcon\Logger\Adapter\File as FileAdapter;


class Application extends BaseApplication
{
	private $_config 			= null;
	private $_databaseConfig 	= null;
	private $_router 			= null;
	protected $_loader			= null;
	protected $_annotations		= null;
	public static $defModule	= 'frontend';
	public static $defNamespace	= '';
	public static $defBaseUri	= '/';
	
	
	public function __construct()
	{
		include_once(CONFIG_SOURCE);
		include_once(DATABASE_CONFIG_SOURCE);

		$this -> _config = new Config($cfg_settings);
		$this -> _databaseConfig = new Config($cfg_database);

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
		$this -> _initAnnotations($di);
		$this -> _initDatabase($di);
		$this -> _initRouter($di);
		$this -> _initUrl($di);
		$this -> _initCache($di);
		$this -> _initModules($di);

		$di -> setShared('app', $this);
		
/*echo '<pre>';
$router =  $di -> get('router');
var_dump($router -> getActionName());
echo '</pre>';
die();*/
		
	}
	
	public function getOutput()
	{
		return $this -> handle() -> getContent();
	}

	protected function _initLoader(\Phalcon\DI $di) {
		if (!$di -> has('loader')) {
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

	
	protected function _initAnnotations(\Phalcon\DI $di)
	{
		$this -> _annotations = new \Core\Annotations($di);
		$this -> _annotations -> run();
				
		$di -> set('annotations', $this -> _annotations);
	}

	protected function _initRouter(\Phalcon\DI $di)
	{
		$this -> _router = new \Phalcon\Mvc\Router\Annotations(false);
		$this -> _router -> removeExtraSlashes(true);
		
		$this -> _router -> setDefaultModule($this -> _config -> application -> defaultModule);
		$this -> _router -> setDefaultNamespace($this -> _config -> application -> defaultNamespace);
		$this -> _router -> setDefaultController($this -> _config -> application -> defaultController);
		$this -> _router -> setDefaultAction($this -> _config -> application -> defaultAction); 		
		
		$routes = $this -> _annotations -> getRoutes();

		if (!empty($routes)) {
			foreach($routes as $link => $route) {
				$this -> _router -> add($link, $route);
			}
		}
		
		/*$this -> _router -> handle();
		$defModule = $this -> _router -> getModuleName();
		if ($defModule === null) {
			$defModule = 'frontend';
		}
		//var_dump($defModule); die();		
		$this -> _router -> setDefaultModule($defModule);
		$this -> _router -> setDefaultNamespace($this -> _config -> modules -> $defModule -> defaultNameSpace);
		$this -> _router -> setDefaultController($this -> _config -> application -> defaultController);
		$this -> _router -> setDefaultAction($this -> _config -> application -> defaultAction); */
		 
		$di -> set('router', $this -> _router);
	}
	

	protected function _initNamespaces($namespaces)
	{
		$this -> _loader -> registerNamespaces($namespaces);
	}
	
	protected function _initUrl(\Phalcon\DI $di)
	{
		if ($this -> _config -> application -> baseUri !== false) {
			$bu = $this -> _config -> application -> baseUri;
		} else {
			$bu = self::$defBaseUri;
		}
		
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
		if (!$di -> has('db')) {

			$adapter = '\Phalcon\Db\Adapter\Pdo\\' . $this -> _databaseConfig -> adapter;
			$config = $this -> _databaseConfig;
			
			$di -> set('db',
				function () use ($config, $adapter) {

                    $eventsManager = new EventsManager();

                    $logger = new FileAdapter(ROOT_APP.'var/logs/sql.log');

                    //Listen all the database events
                    $eventsManager->attach('db', function($event, $connection) use ($logger) {
                            if ($event->getType() == 'beforeQuery') {
                                $logger->log($connection->getSQLStatement());
                            }
                        });

					$connection = new $adapter(
						array('host' => $config -> host,
							  'username' => $config -> username,
							  'password' => $config -> password,
							  'dbname' => $config -> dbname,
                              'charset' => $config->charset,
                              'options' => $config->options->toArray()
						)
					);

                    $connection->setEventsManager($eventsManager);

					return $connection;
				} 
			);
		}
	}

	protected function _initCache(\Phalcon\DI $di)
	{
		$frontCache = new Phalcon\Cache\Frontend\Data(array('lifetime' => $this -> _config -> application -> cache -> lifetime));
		$cache = new Phalcon\Cache\Backend\Memcache($frontCache, array(
			'host' => $this -> _config -> application -> cache -> host,
			'port' => $this -> _config -> application -> cache -> port,
			'persistent' => $this -> _config -> application -> cache -> persistent
		 ));

		$di -> set('cacheData', $cache);

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
		} */
	}
}
