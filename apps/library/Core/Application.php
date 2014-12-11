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
	private $_config 				= null;
	private $_facebookConfig		= null;
	private $_databaseConfig 		= null;
    private $_elasticConfig 		= null;
	private $_router 				= null;
	private $_shardingConfig		= null;
	private $_shardingServiceConfig	= null;
	protected $_loader				= null;
	protected $_annotations			= null;
	public static $defModule		= 'frontend';
	public static $defNamespace		= '';
	public static $defBaseUri		= '/';
	
	
	public function __construct()
	{
		include_once(CONFIG_SOURCE);
		include_once(DATABASE_CONFIG_SOURCE);
		include_once(FACEBOOK_CONFIG_SOURCE);
        include_once(SERVICE_CONFIG_SOURCE);
        include_once(SHARDING_CONFIG_SOURCE);
        include_once(SHARDING_SERVICE_CONFIG_SOURCE);

		$this -> _config = new Config($cfg_settings);
		$this -> _databaseConfigWrite = new Config($cfg_database_master);
		$this -> _databaseConfigRead = new Config($cfg_database_slave);
		$this -> _facebookConfig = new Config($cfg_facebook);
        $this -> _elasticConfig = new Config($cfg_elastic);
        $this -> _shardingConfig = new Config($cfg_sharding);
        $this -> _shardingServiceConfig = new Config($cfg_sharding_service);

		$di = new DIFactory();
		$di -> setShared('config', $this -> _config);
		$di -> setShared('facebook_config', $this -> _facebookConfig);
		$di -> setShared('shardingConfig', $this -> _shardingConfig);
		$di -> setShared('shardingServiceConfig', $this -> _shardingServiceConfig);

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
        $this -> _initElastic($di);

		$di -> setShared('app', $this);
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

            // Loader fo GeoIp MaxMind
            $this->_loader->registerClasses([
                'ComposerAutoloaderInit19cdb3f649c3bc3b13267b71c926c6ce' => ROOT_APP . 'apps/library/Thirdparty/GeoIP2-php/vendor/composer/autoload_real.php'
            ]);
	
			$this -> _loader -> register();

            // Loader fo GeoIp MaxMind
            ComposerAutoloaderInit19cdb3f649c3bc3b13267b71c926c6ce::getLoader();

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
		if (!$di -> has('dbSlave')) {

			$adapter = '\Phalcon\Db\Adapter\Pdo\\' . $this -> _databaseConfigRead -> adapter;
			$config = $this -> _databaseConfigRead;
			
			$di -> set('dbSlave',
				function () use ($config, $adapter) {

                    $eventsManager = new EventsManager();

                    $logger = new FileAdapter(ROOT_APP.'var/logs/sql.log');

                    //Listen all the database events
                    $eventsManager->attach('dbSlave', function($event, $connection) use ($logger) {
                            if ($event->getType() == 'beforeQuery') {
                                $logger->log($connection->getSQLStatement());
                            }
                        });

					$connection = new $adapter(
						array('host' => $config -> host,
							  'username' => $config -> username,
							  'password' => $config -> password,
							  'dbname' => $config -> dbname,
							  'port' => $config -> port,
                              'charset' => $config->charset,
                              'options' => $config->options->toArray()
						)
					);

                    $connection->setEventsManager($eventsManager);

					return $connection;
				}
			);

			$configApp = $this -> _config;			
			$di -> set('modelsMetadata',
					function() use ($configApp) {
						$metaData = new \Phalcon\Mvc\Model\MetaData\Files(array(
								'lifetime' => 86400,
								'prefix' => $configApp -> application -> cache -> cachePrefix,
								'metaDataDir' => $configApp -> application -> cache -> cacheDir
						));
					
						return $metaData;
					}
			);
		}

		if (!$di -> has('dbMaster')) {

			$adapter = '\Phalcon\Db\Adapter\Pdo\\' . $this -> _databaseConfigWrite -> adapter;
			$config = $this -> _databaseConfigWrite;
			
			$di -> set('dbMaster',
				function () use ($config, $adapter) {

                    $eventsManager = new EventsManager();

                    $logger = new FileAdapter(ROOT_APP.'var/logs/sql.log');

                    //Listen all the database events
                    $eventsManager->attach('dbMaster', function($event, $connection) use ($logger) {
                            if ($event->getType() == 'beforeQuery') {
                                $logger->log($connection->getSQLStatement());
                            }
                        });

					$connection = new $adapter(
						array('host' => $config -> host,
							  'username' => $config -> username,
							  'password' => $config -> password,
							  'dbname' => $config -> dbname,
							  'port' => $config -> port,
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
		$frontCache = new Phalcon\Cache\Frontend\Data(['lifetime' => $this -> _config -> application -> cache -> lifetime]);
		
		$cache = new \Core\Cache\Backend\Memcache($frontCache, [
			'host' => $this -> _config -> application -> cache -> host,
			'port' => $this -> _config -> application -> cache -> port,
			'persistent' => $this -> _config -> application -> cache -> persistent,
			'prefix' => $this -> _databaseConfigRead -> dbname
		 ]);


		$di -> set('cacheData', $cache);
	}

    protected function _initElastic(\Phalcon\DI $di)
    {
        $params = $this -> _elasticConfig -> toArray();
        $di->set('elastic', function() use ($params, $di) {
            $config = [
                'index' => $params['index'],
                'connections' => $params['connections']
            ];
            return new \Engine\Search\Elasticsearch\Client($config);
        });
    }
}
